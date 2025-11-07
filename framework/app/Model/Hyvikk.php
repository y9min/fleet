<?php
/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use App\Model\ApiSettings;
use App\Model\ChatSettingsModel;
use App\Model\EmailContent;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\Settings;
use App\Model\TwilioSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Hyvikk {

        // Cache duration in seconds (24 hours)
        private static $cacheDuration = 86400;

        public static function twilio($key) {
                $cacheKey = 'hyvikk_twilio';
                try {
                    $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                        // Use withoutGlobalScopes() to bypass SoftDeletes since deleted_at column may not exist
                        return array_pluck(TwilioSettings::withoutGlobalScopes()->all()->toArray(), 'value', 'name');
                    });
                } catch (\Exception $e) {
                    // Log the error but don't block functionality - twilio settings are optional
                    Log::warning('Failed to load twilio settings: ' . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Return empty array to prevent errors
                    $settings = [];
                }
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function get($key) {
                $cacheKey = 'hyvikk_settings';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(Settings::all()->toArray(), 'value', 'name');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function set($key, $val) {
                $settings = Settings::firstOrNew(array('name' => $key));
                $settings->value = $val;
                $settings->save();
                Cache::forget('hyvikk_settings');
        }

        public static function api($key) {
                $cacheKey = 'hyvikk_api';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(ApiSettings::all()->toArray(), 'key_value', 'key_name');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function fare($key) {
                $key = str_replace(' ', '', $key);
                $cacheKey = 'hyvikk_fare';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(FareSettings::all()->toArray(), 'key_value', 'key_name');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function email_msg($key) {
                $cacheKey = 'hyvikk_email';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(EmailContent::all()->toArray(), 'value', 'key');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function frontend($key) {
                $cacheKey = 'hyvikk_frontend';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(FrontendModel::all()->toArray(), 'key_value', 'key_name');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function payment($key) {
                $cacheKey = 'hyvikk_payment';
                $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                    return array_pluck(PaymentSettings::all()->toArray(), 'value', 'name');
                });
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }

        public static function chat($key) {
                $cacheKey = 'hyvikk_chat';
                try {
                    $settings = Cache::remember($cacheKey, self::$cacheDuration, function () {
                        return array_pluck(ChatSettingsModel::all()->toArray(), 'value', 'name');
                    });
                } catch (\Exception $e) {
                    // Log the error but don't block login - chat settings are non-critical
                    Log::warning('Failed to load chat settings: ' . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Return empty array to prevent login failures
                    $settings = [];
                }
                
                if (is_array($key)) {
                    return array_only($settings, $key);
                }
                return isset($settings[$key]) ? $settings[$key] : '';
        }
}
