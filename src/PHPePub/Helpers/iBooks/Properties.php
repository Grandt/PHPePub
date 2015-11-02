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
abstract class Properties extends Enum {
    /**
     * Enable fixed layout on iBooks
     * Values: true/false
     * Default: false
     */
    const EPUB2_FIXED_LAYOUT = "fixed-layout";

    /**
     * If you use embedded fonts, iBooks requires this property to display them.
     * Values: true/false
     * Default: false
     */
    const EPUB2_SPECIFIED_FONTS = "specified-fonts";

    /**
     * Open the book to a full two-page spread, or zoom to a page?
     * Values: true/false
     * Default: false
     */
    const EPUB2_OPEN_TO_SPREAD = "open-to-spread";

    /**
     * Lock orientation on the devices.
     * Values: "none", "portrait-only", "landscape-only"
     * Default: "none"
     */
    const EPUB2_ORIENTATION_LOCK = "orientation-lock";

    /**
     * Allow active content, such as scripting and canvas.
     * Values: true/false
     * Default: false
     */
    const EPUB2_INTERACTIVE = "interactive";

    /**
     * Enable fixed layout on iBooks
     * Values: true/false
     * Default: false
     */
    const EPUB3_FIXED_LAYOUT = "ibooks:fixed-layout";

    /**
     * Lock orientation on iPhone devices. Omit this property if there is no orientation lock.
     * Values: "portrait-only", "landscape-only"
     * Default: "none"
     */
    const EPUB3_IPHONE_ORIENTATION_LOCK = "ibooks:iphone-orientation-lock";

    /**
     * Lock orientation on iPad devices. Omit this property if there is no orientation lock.
     * Values: "portrait-only", "landscape-only"
     * Default: "none"
     */
    const EPUB3_IPAD_ORIENTATION_LOCK = "ibooks:ipad-orientation-lock";

    /**
     * If you use embedded fonts, iBooks requires this property to display them.
     * Values: true/false
     * Default: false
     */
    const EPUB3_SPECIFIED_FONTS = "ibooks:specified-fonts";

    /**
     * Tell iBooks if it is to display the "faux" book binding.
     * Values: true/false
     * Default: true
     */
    const EPUB3_BINDING = "ibooks:binding";

}
