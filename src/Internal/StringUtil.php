<?php

declare(strict_types=1);

namespace MattHarvey\CivilDateTime\Internal;

class StringUtil
{
    public static function stripPrefix(string $str, string $prefix): string
    {
        $pos = strpos($str, $prefix);
        if ($pos === false) {
            return $str;
        }
        if ($pos == 0) {
            return substr($str, strlen($prefix));
        }
        return $str;
    }
}
