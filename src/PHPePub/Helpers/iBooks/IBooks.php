<?php
namespace PHPePub\Helpers\iBooks;

use PHPePub\Core\EPub;
use PHPePub\Helpers\enums\Bool;

/**
 * Helper for iBooks 2 and 3 books.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009- A. Grandt
 * @license   GNU LGPL 2.1
 */
class IBooks {
    const EPUB2_IBOOK_FILE_NAME = "com.apple.ibooks.display-options.xml";

    /**
     * Add with: $book->addCustomPrefix(iBooks::EPUB3_IBOOK_PREFIX_NAME, iBooks::EPUB3_IBOOK_PREFIX_URI);
     */
    const EPUB3_IBOOK_PREFIX_NAME = "ibooks";
    const EPUB3_IBOOK_PREFIX_URI = "http://vocabulary.itunes.apple.com/rdf/ibooks/vocabulary-extensions-1.0";

    /**
     * Add iBooks prefix to the ePub book
     *
     * @param EPub $book
     */
    function addEPub3IBooksPrefix($book) {
        $book->addCustomPrefix(self::EPUB3_IBOOK_PREFIX_NAME, self::EPUB3_IBOOK_PREFIX_URI);
    }

    /**
     * @param EPub $book
     * @param string $property
     * @param string $value
     */
    function addEPub3IBooksProperty($book, $property, $value) {
        $book->addCustomMetaProperty($property, $value);
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    function setEPub3IBooksFixedLayout($book, $value) {
        $book->addCustomMetaProperty(Properties::EPUB3_FIXED_LAYOUT, $this->getIBookBoolean($value));
    }

    /**
     * @param EPub $book
     * @param string $value "portrait-only", "landscape-only"
     */
    function setEPub3IBooksIPhoneOrientationLock($book, $value) {
        if ($value === "portrait-only" || $value === "landscape-only") {
            $book->addCustomMetaProperty(Properties::EPUB3_IPHONE_ORIENTATION_LOCK, $value);
        }
    }

    /**
     * @param EPub $book
     * @param string $value "portrait-only", "landscape-only"
     */
    function setEPub3IBooksIPadOrientationLock($book, $value) {
        if ($value === "portrait-only" || $value === "landscape-only") {
            $book->addCustomMetaProperty(Properties::EPUB3_IPAD_ORIENTATION_LOCK, $value);
        }
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    function setEPub3IBooksSpecifiedFonts($book, $value) {
        $book->addCustomMetaProperty(Properties::EPUB3_SPECIFIED_FONTS, $this->getIBookBoolean($value));
    }

    /**
     * @param EPub $book
     * @param bool $value
     */
    function setEPub3IBooksBinding($book, $value) {
        $book->addCustomMetaProperty(Properties::EPUB3_BINDING, $this->getIBookBoolean($value));
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getIBookBoolean($value) {
        return $value === true ? Bool::TRUE : Bool::FALSE;
    }
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
