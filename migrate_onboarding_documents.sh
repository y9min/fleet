#!/bin/bash

# Migration script to move onboarding documents from storage to S3 or public/uploads
# This script should be run on the production server (Render.com)

echo "Starting onboarding document migration..."

# Check if S3 is configured
if [ -n "$AWS_BUCKET" ] && [ -n "$AWS_KEY" ] && [ -n "$AWS_SECRET" ]; then
    echo "S3 is configured. Documents will be uploaded to S3."
    USE_S3=true
    S3_BUCKET="$AWS_BUCKET"
    S3_REGION="${AWS_REGION:-us-east-1}"
else
    echo "S3 not configured. Documents will be moved to local public/uploads."
    USE_S3=false
fi

# Source and destination directories
SOURCE_DIR="/var/www/html/storage/app/public/onboarding/documents"
LOCAL_DEST_DIR="/var/www/html/public/uploads/onboarding"

# Check if source directory exists
if [ ! -d "$SOURCE_DIR" ]; then
    echo "Source directory $SOURCE_DIR does not exist. No files to migrate."
    exit 0
fi

# Count files to migrate
file_count=$(find "$SOURCE_DIR" -type f | wc -l)
echo "Found $file_count files to migrate from $SOURCE_DIR"

if [ $file_count -eq 0 ]; then
    echo "No files found to migrate."
    exit 0
fi

if [ "$USE_S3" = true ]; then
    echo "Uploading files to S3 bucket: $S3_BUCKET"
    
    # Install AWS CLI if not present
    if ! command -v aws &> /dev/null; then
        echo "Installing AWS CLI..."
        curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
        unzip awscliv2.zip
        sudo ./aws/install
    fi
    
    # Configure AWS CLI
    aws configure set aws_access_key_id "$AWS_KEY"
    aws configure set aws_secret_access_key "$AWS_SECRET"
    aws configure set default.region "$S3_REGION"
    
    # Upload files to S3
    for file in "$SOURCE_DIR"/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            echo "Uploading $filename to S3..."
            aws s3 cp "$file" "s3://$S3_BUCKET/uploads/onboarding/$filename"
            if [ $? -eq 0 ]; then
                echo "Successfully uploaded $filename"
            else
                echo "Failed to upload $filename"
            fi
        fi
    done
    
    echo "S3 upload completed!"
    echo "Files are now accessible at: https://$S3_BUCKET.s3.$S3_REGION.amazonaws.com/uploads/onboarding/"
    
else
    echo "Moving files to local storage..."
    
    # Create destination directory if it doesn't exist
    mkdir -p "$LOCAL_DEST_DIR"
    
    # Copy files from source to destination
    echo "Copying files..."
    cp -r "$SOURCE_DIR"/* "$LOCAL_DEST_DIR"/ 2>/dev/null || {
        echo "Error copying files. Check permissions."
        exit 1
    }
    
    # Verify files were copied
    copied_count=$(find "$LOCAL_DEST_DIR" -type f | wc -l)
    echo "Successfully copied $copied_count files to $LOCAL_DEST_DIR"
    
    # Set proper permissions
    chown -R www-data:www-data "$LOCAL_DEST_DIR"
    chmod -R 755 "$LOCAL_DEST_DIR"
    
    echo "Local migration completed successfully!"
    echo "Files are now accessible at: https://your-domain.com/uploads/onboarding/"
fi

echo ""
echo "Next steps:"
echo "1. Run the database update SQL to clean up file paths"
echo "2. Test document access in the admin panel"
echo "3. Remove old files from storage directory if everything works"

# Optional: Remove old files (uncomment after testing)
# echo "Removing old files from storage directory..."
# rm -rf "$SOURCE_DIR"
# echo "Old files removed."
