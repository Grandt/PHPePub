<?php
namespace PHPePub\Core\Structure\OPF;

use com\grandt\BinStringStatic;
use PHPePub\Core\EPub;
use PHPePub\Core\StaticData;

/**
 * ePub OPF Metadata structures
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2014- A. Grandt
 * @license   GNU LGPL 2.1
 */
class Metadata {
    private $dc = array();
    private $meta = array();
    private $metaProperties = array();
    public $namespaces = array();

    /**
     * Class constructor.
     */
    function __construct() {
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->dc, $this->meta);
    }

    /**
     *
     * Enter description here ...
     *
     * @param MetaValue $dc
     */
    function addDublinCore($dc) {
        if ($dc != null && is_object($dc) && $dc instanceof MetaValue) {
            $this->dc[] = $dc;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $content
     */
    function addMeta($name, $content) {
        $name = is_string($name) ? trim($name) : null;
        if (isset($name)) {
            $content = is_string($content) ? trim($content) : null;
        }
        if (isset($content)) {
            $this->meta[] = array(
                $name => $content
            );
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $content
     */
    function addMetaProperty($name, $content) {
        $name = is_string($name) ? trim($name) : null;
        if (isset($name)) {
            $content = is_string($content) ? trim($content) : null;
        }
        if (isset($content)) {
            $this->metaProperties[] = array(
                $name => $content
            );
        }
    }

    /**
     * @param string $nsName
     * @param string $nsURI
     */
    function addNamespace($nsName, $nsURI) {
        if (!array_key_exists($nsName, $this->namespaces)) {
            $this->namespaces[$nsName] = $nsURI;
        }
    }

    /**
     *
     * @param string $bookVersion
     * @param int    $date
     *
     * @return string
     */
    function finalize($bookVersion = EPub::BOOK_VERSION_EPUB2, $date = null) {
        if ($bookVersion === EPub::BOOK_VERSION_EPUB2) {
            $this->addNamespace("opf", StaticData::$namespaces["opf"]);
        } else {
            if (!isset($date)) {
                $date = time();
            }
            $this->addNamespace("dcterms", StaticData::$namespaces["dcterms"]);
            $this->addMetaProperty("dcterms:modified", gmdate('Y-m-d\TH:i:s\Z', $date));
        }

        if (count($this->dc) > 0) {
            $this->addNamespace("dc", StaticData::$namespaces["dc"]);
        }

        $metadata = "\t<metadata>\n";

        foreach ($this->dc as $dc) {
            /** @var $dc MetaValue */
            $metadata .= $dc->finalize($bookVersion);
        }

        foreach ($this->metaProperties as $data) {
            $content = reset($data);
            $name = key($data);
            $metadata .= "\t\t<meta property=\"" . $name . "\">" . $content . "</meta>\n";
        }

        foreach ($this->meta as $data) {
            $content = reset($data);
            $name = key($data);
            $metadata .= "\t\t<meta name=\"" . $name . "\" content=\"" . $content . "\" />\n";
        }

        return $metadata . "\t</metadata>\n";
    }
}
