<?php
namespace PHPePub\Core\Structure\OPF;

/**
 * ePub OPF Dublin Core (dc:) Metadata structures
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2014- A. Grandt
 * @license   GNU LGPL 2.1
 */
class DublinCore extends MetaValue {
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

    /**
     * Class constructor.
     *
     * @param string $name
     * @param string $value
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
        if (is_string($name)) {
            $this->setValue("dc:" . trim($name), $value);
        }
    }
}
