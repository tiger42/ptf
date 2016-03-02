<?php
/**
 * Collection of miscellaneous helper functions
 */

namespace Ptf\Util;

/**
 * Get the current date and time in database format
 *
 * @return  string                      The current timestamp
 */
function now()
{
    return date('Y-m-d H:i:s');
}

/**
 * Determine whether the given value is an integer number (in the mathematical sense)
 *
 * @param   mixed $val                  The value to check
 * @return  boolean                     Is the value a whole number?
 */
function isIntegerNumber($val)
{
    return is_numeric($val) && (int)$val == (float)$val;
}

/**
 * Determine whether the given number is even
 *
 * @param   integer $number             The number to check
 * @return  boolean                     Is the number even?
 */
function even($number)
{
    return isIntegerNumber($number) && !($number & 1);
}

/**
 * Determine whether the given number is odd
 *
 * @param   integer $number             The number to check
 * @return  boolean                     Is the number odd?
 */
function odd($number)
{
    return isIntegerNumber($number) && ($number & 1);
}

/**
 * Check whether the given array is a numeric (non associative) array
 *
 * @param   array $array                The array to check
 * @return  boolean                     Is the array a numeric array?
 */
function isNumericArray(array $array)
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
 * Truncate the given string
 *
 * @param   string $string              The string to truncate
 * @param   integer $length             The desired number of characters
 * @param   string $etc                 String to append to the end of the truncated string
 * @return  string                      The truncated string
 */
function truncate($string, $length, $etc = '')
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
 * Convert the given string with underscores (or any individual separator) to camel case
 *
 * @param   string $string              The string to convert
 * @param   boolean $lcFirst            Convert the first character to lower case?
 * @param   string $separator           The word separator
 * @return  string                      The camelized string
 */
function camelize($string, $lcFirst = false, $separator = '_')
{
    $string = str_replace(' ', '', ucwords(str_replace($separator, ' ', $string)));

    if ($lcFirst) {
        $string = lcfirst($string);
    }

    return $string;
}

/**
 * Convert the given camel cased string to an underscore (or individual separator) separated string
 *
 * @param   string $string              The string to convert
 * @param   string $separator           The word separator
 * @return  string                      The uncamelized string
 */
function uncamelize($string, $separator = '_')
{
   return strtolower(preg_replace('/([A-Z])/', $separator . '$1', $string));
}

/**
 * Pretty print the given JSON string
 *
 * @param   string $json                The JSON to pretty print
 * @return  string                      The pretty printed JSON
 */
function prettyPrintJson($json)
{
    return json_encode(json_decode($json), JSON_PRETTY_PRINT);
}
