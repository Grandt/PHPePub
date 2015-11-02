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
abstract class Bool extends Enum {
    const TRUE = "true";
    const FALSE = "false";
}
