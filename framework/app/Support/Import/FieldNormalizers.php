<?php

namespace App\Support\Import;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FieldNormalizers {
    public static function trimOrNull($value) {
        if ($value === null) { return null; }
        $v = is_string($value) ? trim($value) : $value;
        return $v === '' ? null : $v;
    }

    public static function toBoolean($value) {
        if (is_bool($value)) { return $value; }
        if ($value === null) { return null; }
        $v = strtolower(trim((string)$value));
        $truthy = ['true','1','yes','y','on'];
        $falsy  = ['false','0','no','n','off'];
        if (in_array($v, $truthy, true)) { return true; }
        if (in_array($v, $falsy, true)) { return false; }
        return null;
    }

    public static function toInt($value) {
        if ($value === null || $value === '') { return null; }
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function toFloat($value) {
        if ($value === null || $value === '') { return null; }
        return (float) str_replace([','], [''], (string)$value);
    }

    public static function toDate($value) {
        if (!$value) { return null; }
        try {
            // Handle Excel serial date numbers (e.g., 44078)
            if (is_int($value) || (is_string($value) && is_numeric($value)) || is_float($value)) {
                $num = (float) $value;
                // Excel uses 1900-based serial dates; valid serials are typically > 25569 (1970-01-01)
                if ($num > 0) {
                    try {
                        return ExcelDate::excelToDateTimeObject($num)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        // fall through to string parsing
                    }
                }
            }

            $value = trim((string)$value);
            
            // Handle DD/MM/YYYY format explicitly
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                $parts = explode('/', $value);
                $day = (int)$parts[0];
                $month = (int)$parts[1];
                $year = (int)$parts[2];
                
                // Validate date components
                if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 1900 && $year <= 2100) {
                    return Carbon::create($year, $month, $day)->format('Y-m-d');
                }
            }
            
            // Try Carbon's automatic parsing for other formats (YYYY-MM-DD, etc.)
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function toEnum($value, array $allowed) {
        if ($value === null) { return null; }
        foreach ($allowed as $opt) {
            if (strcasecmp((string)$value, (string)$opt) === 0) { return $opt; }
        }
        return null;
    }

    public static function toUpper($value) {
        return $value === null ? null : strtoupper(trim((string)$value));
    }

    public static function email($value) {
        return $value === null ? null : strtolower(trim((string)$value));
    }
}


