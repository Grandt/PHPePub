<?php
namespace PHPePub\Helpers\enums;

use PHPePub\Helpers\Enum;

/**
 * Why this enum? Have you never made a typo like treu or flase in 'boolean' text parameters?
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */
abstract class Boolean extends Enum {
    const TRUE = "true";
    const FALSE = "false";

    /**
     * @param bool $value
     *
     * @return string constant
     */
    public static function getBoolean($value) {
        if (is_bool($value)) {
            return $value === true ? self::TRUE : self::FALSE;
        }
        if (is_numeric($value)) { // 0 is false, everything else is true.
            return $value !== 0 ? self::TRUE : self::FALSE;
        }
        if (is_string($value)) { // 0 is false, everything else is true.
            $value = strtolower($value);

            return $value === "1"
            || $value === "t"
            || $value === "true"
                ? self::TRUE : self::FALSE;
        }

        return self::FALSE;
    }
}
