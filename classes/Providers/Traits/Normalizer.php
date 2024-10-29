<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers\Traits;

trait Normalizer
{
    /**
     * This function sanitizes a given string by fixing placeholders.
     * It replaces placeholders like %s or %d with their lowercase equivalents
     * and handles placeholders of the form %n$s or %n$d.
     *
     * @param string $string the input string to be sanitized
     *
     * @return string the sanitized string
     */
    protected function normalizePlaceholders($string)
    {
        $string = preg_replace_callback('/% (s|d)/i', function ($m) {
            return '"%".strtolower($m[1])';
        }, $string);

        return preg_replace_callback('/% (\d+) \$ (s|d)/i', function ($m) {
            return '"%".$m[1]."\\$".strtolower($m[2])';
        }, $string);
    }
}
