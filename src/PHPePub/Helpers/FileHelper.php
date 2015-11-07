<?php
/**
 * PHPePub
 * <FileHelper.php description here>
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */

namespace PHPePub\Helpers;


use PHPePub\Core\EPub;
use PHPePub\Core\StaticData;
use RelativePath;

class FileHelper {
    protected static $isCurlInstalled;
    protected static $isFileGetContentsInstalled;
    protected static $isFileGetContentsExtInstalled;

    /**
     * @return mixed
     */
    public static function getIsCurlInstalled() {
        if (!isset(self::$isCurlInstalled)) {
            self::$isCurlInstalled = extension_loaded('curl') && function_exists('curl_version');
        }
        return self::$isCurlInstalled;
    }

    /**
     * @return mixed
     */
    public static function getIsFileGetContentsInstalled() {
        if (!isset(self::$isFileGetContentsInstalled)) {
            self::$isFileGetContentsInstalled = function_exists('file_get_contents');
        }
        return self::$isFileGetContentsInstalled;
    }

    /**
     * @return mixed
     */
    public static function getIsFileGetContentsExtInstalled() {
        if (!isset(self::$isFileGetContentsExtInstalled)) {
            self::$isFileGetContentsExtInstalled = self::getIsFileGetContentsInstalled() && ini_get('allow_url_fopen');
        }
        return self::$isFileGetContentsExtInstalled;
    }

    /**
     * Remove disallowed characters from string to get a nearly safe filename
     *
     * @param string $fileName
     *
     * @return mixed|string
     */
    public static function sanitizeFileName($fileName) {
        $fileName1 = str_replace(StaticData::$forbiddenCharacters, '', $fileName);
        $fileName2 = preg_replace('/[\s-]+/', '-', $fileName1);

        return trim($fileName2, '.-_');
    }

    /**
     * Get file contents, using curl if available, else file_get_contents
     *
     * @param string $source
     * @param bool   $toTempFile
     *
     * @return bool|mixed|null|string
     */
    public static function getFileContents($source, $toTempFile = false) {
        $isExternal = preg_match('#^(http|ftp)s?://#i', $source) == 1;

        if ($isExternal && FileHelper::getIsCurlInstalled()) {
            $ch = curl_init();
            $outFile = null;
            $fp = null;

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL, str_replace(" ", "%20", $source));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BUFFERSIZE, 4096);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
            curl_setopt($ch, CURLOPT_ENCODING, ""); // handle all encodings
            curl_setopt($ch, CURLOPT_USERAGENT, "EPub (Version " . EPub::VERSION . ") by A. Grandt"); // who am i
            curl_setopt($ch, CURLOPT_AUTOREFERER, true); // set referer on redirect
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // timeout on connect
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // timeout on response
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // stop after 10 redirects
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disabled SSL Cert checks

            if ($toTempFile) {
                $outFile = tempnam(sys_get_temp_dir(), "EPub_v" . EPub::VERSION . "_");
                $fp = fopen($outFile, "w+b");
                curl_setopt($ch, CURLOPT_FILE, $fp);

                $res = curl_exec($ch);
                $info = curl_getinfo($ch);

                curl_close($ch);
                fclose($fp);
            } else {
                $res = curl_exec($ch);
                $info = curl_getinfo($ch);

                curl_close($ch);
            }

            if ($info['http_code'] == 200 && $res != false) {
                if ($toTempFile) {
                    return $outFile;
                }

                return $res;
            }

            return false;
        }

        if (FileHelper::getIsFileGetContentsInstalled() && (!$isExternal || FileHelper::getIsFileGetContentsExtInstalled())) {
            @$data = file_get_contents($source);

            return $data;
        }

        return false;
    }

    /**
     * Cleanup the filepath, and remove leading . and / characters.
     *
     * Sometimes, when a path is generated from multiple fragments,
     *  you can get something like "../data/html/../images/image.jpeg"
     * ePub files don't work well with that, this will normalize that
     *  example path to "data/images/image.jpeg"
     *
     * @param string $fileName
     *
     * @return string normalized filename
     */
    public static function normalizeFileName($fileName) {
        return preg_replace('#^[/\.]+#i', "", RelativePath::getRelativePath($fileName));
    }
}
