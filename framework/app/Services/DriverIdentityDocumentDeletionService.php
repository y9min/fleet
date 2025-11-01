<?php

namespace App\Services;

use App\Model\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverIdentityDocumentDeletionService
{
    /**
     * Delete all identity documents for a driver from S3 and database
     * 
     * @param User $driver
     * @return array Result with success status, deleted_files_count, and errors
     */
    public function deleteDriverIdentityDocuments(User $driver): array
    {
        // Check if documents have already been deleted
        $alreadyDeleted = $driver->getMeta('identity_docs_deleted');
        if ($alreadyDeleted === '1' || $alreadyDeleted === true || $alreadyDeleted === 'true') {
            Log::info('Driver identity documents already deleted', [
                'driver_id' => $driver->id,
                'driver_email' => $driver->email
            ]);
            return [
                'success' => true,
                'already_deleted' => true,
                'deleted_files_count' => 0,
                'errors' => []
            ];
        }

        $deletedFilesCount = 0;
        $errors = [];
        $documentFields = [
            'license_image',
            'license_upload_path',
            'insurance_upload_path',
            'documents',
            'driver_image'
        ];

        // Check if S3 is configured
        $useS3 = env('AWS_BUCKET') && (env('AWS_ACCESS_KEY_ID') || env('AWS_KEY')) && (env('AWS_SECRET_ACCESS_KEY') || env('AWS_SECRET'));
        
        if (!$useS3) {
            Log::warning('S3 not configured, skipping S3 deletion', [
                'driver_id' => $driver->id
            ]);
        }

        // Collect all document paths/URLs to delete
        $filesToDelete = [];

        // Get document paths from metadata fields
        foreach ($documentFields as $field) {
            $value = $driver->getMeta($field);
            if ($value && !empty($value)) {
                $filesToDelete[] = [
                    'source' => $field,
                    'path' => $value
                ];
            }
        }

        // Check custom_data for additional document URLs
        $customData = $driver->getMeta('custom_data');
        if ($customData) {
            $customDataArray = is_string($customData) ? json_decode($customData, true) : $customData;
            if (is_array($customDataArray)) {
                foreach ($customDataArray as $key => $value) {
                    // Look for fields that might contain document URLs/paths
                    if (is_string($value) && (
                        stripos($key, 'doc') !== false ||
                        stripos($key, 'upload') !== false ||
                        stripos($key, 'image') !== false ||
                        stripos($key, 'proof') !== false ||
                        stripos($key, 'license') !== false ||
                        stripos($key, 'id_') !== false
                    )) {
                        // Check if it's a URL or path
                        if (filter_var($value, FILTER_VALIDATE_URL) || 
                            preg_match('/\.(jpg|jpeg|png|pdf|doc|docx)$/i', $value)) {
                            $filesToDelete[] = [
                                'source' => "custom_data.{$key}",
                                'path' => $value
                            ];
                        }
                    }
                }
            }
        }

        // Delete files from S3
        if ($useS3) {
            foreach ($filesToDelete as $fileInfo) {
                try {
                    $s3Key = $this->extractS3Key($fileInfo['path'], $driver->id);
                    
                    if ($s3Key) {
                        $exists = Storage::disk('s3')->exists($s3Key);
                        if ($exists) {
                            Storage::disk('s3')->delete($s3Key);
                            $deletedFilesCount++;
                            Log::info('Deleted file from S3', [
                                'driver_id' => $driver->id,
                                's3_key' => $s3Key,
                                'source_field' => $fileInfo['source']
                            ]);
                        } else {
                            Log::info('File not found in S3 (may already be deleted)', [
                                'driver_id' => $driver->id,
                                's3_key' => $s3Key,
                                'source_field' => $fileInfo['source']
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Failed to delete file from S3: {$e->getMessage()}";
                    $errors[] = [
                        'file' => $fileInfo['path'],
                        'source' => $fileInfo['source'],
                        'error' => $errorMsg
                    ];
                    Log::warning($errorMsg, [
                        'driver_id' => $driver->id,
                        'file_path' => $fileInfo['path'],
                        'exception' => $e->getMessage()
                    ]);
                }
            }
        }

        // Update metadata to null out document fields
        $metadataToNull = [];
        foreach ($documentFields as $field) {
            if ($driver->getMeta($field)) {
                $metadataToNull[$field] = null;
            }
        }

        // Also clean up any document references in custom_data
        if ($customData) {
            $customDataArray = is_string($customData) ? json_decode($customData, true) : $customData;
            if (is_array($customDataArray)) {
                $updated = false;
                foreach ($customDataArray as $key => $value) {
                    if (is_string($value) && (
                        stripos($key, 'doc') !== false ||
                        stripos($key, 'upload') !== false ||
                        stripos($key, 'image') !== false ||
                        stripos($key, 'proof') !== false ||
                        stripos($key, 'license') !== false ||
                        stripos($key, 'id_') !== false
                    )) {
                        if (filter_var($value, FILTER_VALIDATE_URL) || 
                            preg_match('/\.(jpg|jpeg|png|pdf|doc|docx)$/i', $value)) {
                            $customDataArray[$key] = null;
                            $updated = true;
                        }
                    }
                }
                if ($updated) {
                    $metadataToNull['custom_data'] = json_encode($customDataArray);
                }
            }
        }

        // Set deletion tracking metadata
        $metadataToNull['identity_docs_deleted'] = true;
        $metadataToNull['identity_docs_deleted_at'] = now()->toDateTimeString();

        // Update metadata
        if (!empty($metadataToNull)) {
            $driver->setMeta($metadataToNull);
        }

        // Create audit log entry
        try {
            DB::table('driver_identity_deletions')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'driver_id' => $driver->id,
                'deleted_by' => 'system',
                'deleted_at' => now(),
                'deleted_files_count' => $deletedFilesCount,
                'error_log' => !empty($errors) ? json_encode($errors) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create audit log entry', [
                'driver_id' => $driver->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = [
                'action' => 'audit_log',
                'error' => "Failed to create audit log: {$e->getMessage()}"
            ];
        }

        Log::info('Completed driver identity document deletion', [
            'driver_id' => $driver->id,
            'driver_email' => $driver->email,
            'deleted_files_count' => $deletedFilesCount,
            'errors_count' => count($errors)
        ]);

        return [
            'success' => true,
            'deleted_files_count' => $deletedFilesCount,
            'errors' => $errors
        ];
    }

    /**
     * Extract S3 key from a path or URL
     * 
     * @param string $pathOrUrl
     * @param string $driverId
     * @return string|null
     */
    private function extractS3Key(string $pathOrUrl, string $driverId): ?string
    {
        // If it's a full URL, extract the path
        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            // Extract path from URL (e.g., https://bucket.s3.region.amazonaws.com/uploads/file.jpg)
            $parsed = parse_url($pathOrUrl);
            $path = ltrim($parsed['path'] ?? '', '/');
            
            // Remove bucket name if it's in the path
            $bucketName = env('AWS_BUCKET');
            if ($bucketName && strpos($path, $bucketName) === 0) {
                $path = substr($path, strlen($bucketName) + 1);
            }
            
            return $path;
        }

        // If it's already a relative path
        // Remove leading slashes
        $path = ltrim($pathOrUrl, '/');
        
        // If path starts with 'uploads/', use as-is
        if (strpos($path, 'uploads/') === 0) {
            return $path;
        }
        
        // If path starts with 'onboarding/', use as-is (might be from onboarding process)
        if (strpos($path, 'onboarding/') === 0) {
            return $path;
        }
        
        // If it's just a filename, construct path
        if (!strpos($path, '/')) {
            // Check if it should be in uploads/{driver_id}/ or just uploads/
            return "uploads/{$driverId}/{$path}";
        }
        
        // Otherwise, try to construct a path
        // If it doesn't start with uploads, prepend it
        if (strpos($path, 'uploads/') !== 0) {
            return "uploads/{$path}";
        }
        
        return $path;
    }
}

