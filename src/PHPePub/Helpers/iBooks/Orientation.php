<?php
namespace PHPePub\Helpers\iBooks;

use PHPePub\Helpers\Enum;

/**
 * Helper to generate com.apple.ibooks.display-options.xml for ePub2 books.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009- A. Grandt
 * @license   GNU LGPL 2.1
 */
abstract class Orientation extends Enum {
    const NONE = "none";
    const PORTRAIT_ONLY = "portrait-only";
    const LANDSCAPE_ONLY = "landscape-only";
}
