<?php

declare(strict_types=1);

function normalizeUtf8Value($value)
{
    if ($value === null) {
        return null;
    }

    if (is_array($value)) {
        $result = [];

        foreach ($value as $key => $item) {
            $result[$key] = normalizeUtf8Value($item);
        }

        return $result;
    }

    if (!is_string($value)) {
        return $value;
    }

    if (mb_check_encoding($value, 'UTF-8')) {
        return $value;
    }

    $converted = @mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');

    if ($converted === false) {
        $fallback = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        if ($fallback === false) {
            return '';
        }

        return $fallback;
    }

    return $converted;
}