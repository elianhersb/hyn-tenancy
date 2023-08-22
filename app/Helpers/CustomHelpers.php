<?php

    if (!function_exists('createUuid')) {
        function createUuid($name)
        {
            $replace = 'tenancy_' . str_replace(' ', '_', $name);
            $limit = Str::limit($replace, 22, '');
            $remainingLength = 22 - strlen($limit);
            $randomDigits = strval(random_int(pow(10, $remainingLength - 1), pow(10, $remainingLength) - 1));

            return $limit . '_' . $randomDigits;
        }
    }