<?php
namespace PHPePub\Helpers\iBooks;

use PHPePub\Core\EPub;
use PHPePub\Helpers\enums\Boolean;

/**
 * Helper for iBooks 2 and 3 books.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009- A. Grandt
 * @license   GNU LGPL 2.1
 */
class IBooksHelper {
    const EPUB2_IBOOK_FILE_NAME = "com.apple.ibooks.display-options.xml";

    /**
     * Add with: $book->addCustomPrefix(iBooks::EPUB3_IBOOK_PREFIX_NAME, iBooks::EPUB3_IBOOK_PREFIX_URI);
     */
    const EPUB3_IBOOK_PREFIX_NAME = "ibooks";
    const EPUB3_IBOOK_PREFIX_URI = "http://vocabulary.itunes.apple.com/rdf/ibooks/vocabulary-extensions-1.0";

    const ORIENTATION_NONE = "none";
    const ORIENTATION_PORTRAIT_ONLY = "portrait-only";
    const ORIENTATION_LANDSCAPE_ONLY = "landscape-only";

    const PLATFORM_ALL = "*";
    const PLATFORM_IPHONE = "iphone";
    const PLATFORM_IPAD = "ipad";

    /**
     * Add iBooks prefix to the ePub book
     *
     * @param EPub $book
     */
    public static function addPrefix($book) {
        if (!$book->isEPubVersion2()) {
            $book->addCustomPrefix(self::EPUB3_IBOOK_PREFIX_NAME, self::EPUB3_IBOOK_PREFIX_URI);
        }
    }

    /**
     * @param EPub   $book
     * @param string $property
     * @param string $value
     */
    public static function addProperty($book, $property, $value) {
        if (!$book->isEPubVersion2()) {
            $book->addCustomMetaProperty($property, $value);
        }
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    public static function setFixedLayout($book, $value) {
        if (!$book->isEPubVersion2()) {
            $book->addCustomMetaProperty(Properties::EPUB3_FIXED_LAYOUT, Boolean::getBoolean($value));
        }
    }

    /**
     * @param EPub   $book
     * @param string $value "portrait-only", "landscape-only"
     */
    public static function setIPhoneOrientationLock($book, $value) {
        if (!$book->isEPubVersion2() && $value === self::ORIENTATION_PORTRAIT_ONLY || $value === self::ORIENTATION_LANDSCAPE_ONLY) {
            $book->addCustomMetaProperty(Properties::EPUB3_IPHONE_ORIENTATION_LOCK, $value);
        }
    }

    /**
     * @param EPub   $book
     * @param string $value "portrait-only", "landscape-only"
     */
    public static function setIPadOrientationLock($book, $value) {
        if (!$book->isEPubVersion2() && $value === self::ORIENTATION_PORTRAIT_ONLY || $value === self::ORIENTATION_LANDSCAPE_ONLY) {
            $book->addCustomMetaProperty(Properties::EPUB3_IPAD_ORIENTATION_LOCK, $value);
        }
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    public static function setSpecifiedFonts($book, $value) {
        if (!$book->isEPubVersion2()) {
            $book->addCustomMetaProperty(Properties::EPUB3_SPECIFIED_FONTS, Boolean::getBoolean($value));
        }
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    public static function setBinding($book, $value) {
        if (!$book->isEPubVersion2()) {
            $book->addCustomMetaProperty(Properties::EPUB3_BINDING, Boolean::getBoolean($value));
        }
    }

    // TODO Add ePub2 implementation is the iBooks xml file.
    /*
        <display_options>
             <platform name="*">
                  <option name="fixed-layout">true</option>
                  <option name="specified-fonts">true</option>
             </platform>
             <platform name="ipad">
                  <option name="open-to-spread">true</option>
                  <option name="orientation-lock">landscape-only"</option>
                  <option name="interactive">true</option>
             </platform>
        </display_options>

        http://www.fantasycastlebooks.com/Tutorials/ibooks-tutorial-part2.html
        http://www.fantasycastlebooks.com/Tutorials/ibooks-tutorial-update.html
     */
}
