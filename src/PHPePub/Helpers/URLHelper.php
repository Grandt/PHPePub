<?php
/**
 * PHPePub
 * <URLHelper.php description here>
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */

namespace PHPePub\Helpers;


class URLHelper {

    /**
     * Get the url of the current page.
     * Example use: Default Source URL
     *
     * $return string Page URL.
     */
    public static function getCurrentPageURL() {
        $pageURL = self::getCurrentServerURL() . filter_input(INPUT_SERVER, "REQUEST_URI");

        return $pageURL;
    }

    /**
     * Get the url of the server.
     * Example use: Default Publisher URL
     *
     * $return string Server URL.
     */
    public static function getCurrentServerURL() {
        $serverURL = 'http';
        $https = filter_input(INPUT_SERVER, "HTTPS");
        $port = filter_input(INPUT_SERVER, "SERVER_PORT");

        if ($https === "on") {
            $serverURL .= "s";
        }
        $serverURL .= "://" . filter_input(INPUT_SERVER, "SERVER_NAME");
        if ($port != "80") {
            $serverURL .= ":" . $port;
        }

        return $serverURL . '/';
    }
}
