<?php
/**
 * Collection of miscellaneous helper functions.
 */

namespace Ptf\Util;

/**
 * Get the current date and time in database format.
 *
 * @return string  The current timestamp
 */
function now(): string
{
    return date('Y-m-d H:i:s');
}

/**
 * Determine whether the given value is an integer number (in the mathematical sense).
 *
 * @param mixed $val  The value to check
 *
 * @return bool  Is the value a whole number?
 */
function isIntegerNumber($val): bool
{
    return is_numeric($val) && (int)$val == (float)$val;
}

/**
 * Determine whether the given number is even.
 *
 * @param float $number  The number to check
 *
 * @return bool  Is the number even?
 */
function even(float $number): bool
{
    return isIntegerNumber($number) && !($number & 1);
}

/**
 * Determine whether the given number is odd.
 *
 * @param float $number  The number to check
 *
 * @return bool  Is the number odd?
 */
function odd(float $number): bool
{
    return isIntegerNumber($number) && ($number & 1);
}

/**
 * Check whether the given array is a numeric (non associative) array.
 *
 * @param array $array  The array to check
 *
 * @return bool  Is the array a numeric array?
 */
function isNumericArray(array $array): bool
{
    $keys = array_keys($array);
    foreach ($keys as $key) {
        if (!is_int($key)) {
            return false;
        }
    }

    return true;
}

/**
 * Truncate the given string.
 *
 * @param string $string  The string to truncate
 * @param int    $length  The desired number of characters
 * @param string $etc     String to append to the end of the truncated string
 *
 * @return string  The truncated string
 */
function truncate(string $string, int $length, string $etc = ''): string
{
    if ($length >= strlen($string)) {
        return $string;
    }

    if (strlen($etc) > $length) {
        $etc = substr($etc, 0, $length);
    }

    return substr($string, 0, $length - strlen($etc)) . $etc;
}

/**
 * Convert the given string with underscores (or any individual separator) to camel case.
 *
 * @param string $string     The string to convert
 * @param bool   $lcFirst    Convert the first character to lower case?
 * @param string $separator  The word separator
 *
 * @return string  The camelized string
 */
function camelize(string $string, bool $lcFirst = false, string $separator = '_'): string
{
    $string = str_replace(' ', '', ucwords(str_replace($separator, ' ', $string)));

    if ($lcFirst) {
        $string = lcfirst($string);
    }

    return $string;
}

/**
 * Convert the given camel cased string to an underscore (or individual separator) separated string.
 *
 * @param string $string     The string to convert
 * @param string $separator  The word separator
 *
 * @return string  The uncamelized string
 */
function uncamelize(string $string, string $separator = '_'): string
{
    return strtolower(preg_replace('/([A-Z])/', $separator . '$1', $string));
}

/**
 * Pretty print the given JSON string.
 *
 * @param string $json  The JSON to pretty print
 *
 * @return string  The pretty printed JSON
 */
function prettyPrintJson(string $json): string
{
    return json_encode(json_decode($json), JSON_PRETTY_PRINT);
}
