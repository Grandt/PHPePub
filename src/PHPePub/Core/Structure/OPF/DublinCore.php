<?php
namespace PHPePub\Core\Structure\OPF;

use PHPePub\Core\EPub;

/**
 * ePub OPF Dublin Core (dc:) Metadata structures
 */
class DublinCore {
    const _VERSION = 3.30;

    const CONTRIBUTOR = "contributor";
    const COVERAGE = "coverage";
    const CREATOR = "creator";
    const DATE = "date";
    const DESCRIPTION = "description";
    const FORMAT = "format";
    const IDENTIFIER = "identifier";
    const LANGUAGE = "language";
    const PUBLISHER = "publisher";
    const RELATION = "relation";
    const RIGHTS = "rights";
    const SOURCE = "source";
    const SUBJECT = "subject";
    const TITLE = "title";
    const TYPE = "type";

    private $dcName = null;
    private $dcValue = null;
    private $attr = array();
    private $opfAttr = array();

    /**
     * Class constructor.
     */
    function __construct($name, $value) {
        $this->setDc($name, $value);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $value
     */
    function setDc($name, $value) {
        $this->dcName = is_string($name) ? trim($name) : null;
        if (isset($this->dcName)) {
            $this->dcValue = isset($value) ? (string)$value : null;
        }
        if (!isset($this->dcValue)) {
            $this->dcName = null;
        }
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->dcName, $this->dcValue, $this->attr, $this->opfAttr);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $attrName
     * @param string $attrValue
     */
    function addAttr($attrName, $attrValue) {
        $attrName = is_string($attrName) ? trim($attrName) : null;
        if (isset($attrName)) {
            $attrValue = is_string($attrValue) ? trim($attrValue) : null;
        }
        if (isset($attrValue)) {
            $this->attr[$attrName] = $attrValue;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $opfAttrName
     * @param string $opfAttrValue
     */
    function addOpfAttr($opfAttrName, $opfAttrValue) {
        $opfAttrName = is_string($opfAttrName) ? trim($opfAttrName) : null;
        if (isset($opfAttrName)) {
            $opfAttrValue = is_string($opfAttrValue) ? trim($opfAttrValue) : null;
        }
        if (isset($opfAttrValue)) {
            $this->opfAttr[$opfAttrName] = $opfAttrValue;
        }
    }

    /**
     *
     * @param string $bookVersion
     *
     * @return string
     */
    function finalize($bookVersion = EPub::BOOK_VERSION_EPUB2) {
        $dc = "\t\t<dc:" . $this->dcName;

        if (sizeof($this->attr) > 0) {
            while (list($name, $content) = each($this->attr)) {
                $dc .= " " . $name . "=\"" . $content . "\"";
            }
        }

        if ($bookVersion === EPub::BOOK_VERSION_EPUB2 && sizeof($this->opfAttr) > 0) {
            while (list($name, $content) = each($this->opfAttr)) {
                $dc .= " opf:" . $name . "=\"" . $content . "\"";
            }
        }

        return $dc . ">" . $this->dcValue . "</dc:" . $this->dcName . ">\n";
    }
}
