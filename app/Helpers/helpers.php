<?php
if (!function_exists('getSetting')) {
    function getSetting($key, $default = null) {
        return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
    }
}
