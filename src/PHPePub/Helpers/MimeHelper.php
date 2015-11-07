<?php
/**
 * PHPePub
 * <MimeHelper.php description here>
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */

namespace PHPePub\Helpers;


use PHPePub\Core\StaticData;

class MimeHelper {

    /**
     * @param string $source URL Source
     *
     * @return string MimeType
     */
    public static function getMimeTypeFromUrl($source) {
        $ext = false;

        $srev = strrev($source);
        $pos = strpos($srev, "?");
        if ($pos !== false) {
            $srev = substr($srev, $pos + 1);
        }

        $pos2 = strpos($srev, ".");
        if ($pos2 !== false) {
            $ext = strtolower(strrev(substr($srev, 0, $pos2)));
        }

        if ($ext !== false) {
            return self::getMimeTypeFromExtension($ext);
        }

        return "application/octet-stream";
    }


    /**
     * @param string $ext Extension
     *
     * @return string MimeType
     */
    public static  function getMimeTypeFromExtension($ext) {
        if (array_key_exists($ext, StaticData::$mimetypes)) {
            return StaticData::$mimetypes[$ext];
        }

        return "application/octet-stream";
    }

    /**
     * Try to determine the mimetype of the file path.
     *
     * @param string $source Path
     *
     * @return string mimetype, or FALSE.
     * @deprecated Use getMimeTypeFromExtension(string $extension) instead.
     */
    public static function getMime($source) {
        return MimeHelper::getMimeTypeFromExtension(pathinfo($source, PATHINFO_EXTENSION));
    }
}
