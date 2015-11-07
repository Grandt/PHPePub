<?php
/**
 * PHPePub
 * <CalibreHelper.php description here>
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */

namespace PHPePub\Helpers;


use PHPePub\Core\EPub;

class CalibreHelper {
    /**
     * @param EPub   $book
     * @param string $seriesName
     * @param string $seriesIndex
     * @param string $sortTitle
     */
    public static function setCalibreMetadata($book, $seriesName, $seriesIndex, $sortTitle = null) {
        $book->addCustomMetadata("calibre:series", $seriesName);
        $book->addCustomMetadata("calibre:series_index", "" . $seriesIndex);
        if (!empty($sortTitle)) {
            $book->addCustomMetadata("calibre:title_sort", $sortTitle);
        }
    }
}
