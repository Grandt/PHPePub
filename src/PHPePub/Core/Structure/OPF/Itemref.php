<?php
namespace PHPePub\Core\Structure\OPF;

/**
 * ePub OPF Itemref structure
 */
class Itemref {
    const _VERSION = 3.30;

    private $idref = NULL;
    private $linear = TRUE;

    /**
     * Class constructor.
     */
    function __construct($idref, $linear = TRUE) {
        $this->setIdref($idref);
        $this->setLinear($linear);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->idref, $this->linear);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $idref
     */
    function setIdref($idref) {
        $this->idref = is_string($idref) ? trim($idref) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @return string $idref
     */
    function getIdref() {
        return $this->idref;
    }

    /**
     *
     * Enter description here ...
     *
     * @param bool $linear
     */
    function setLinear($linear = TRUE) {
        $this->linear = $linear === TRUE;
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $itemref = "\t\t<itemref idref=\"" . $this->idref . "\"";
        return $itemref . ($this->linear == FALSE ? ' linear="no"' : '') . " />\n";
    }
}
