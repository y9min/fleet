<?php

namespace App\Support\Import;

use Carbon\Carbon;

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


