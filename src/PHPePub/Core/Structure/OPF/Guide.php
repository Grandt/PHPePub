<?php
namespace PHPePub\Core\Structure\OPF;

/**
 * ePub OPF Guide structure
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2014- A. Grandt
 * @license   GNU LGPL 2.1
 */
class Guide {

    private $references = array();

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
        unset($this->references);
    }

    /**
     *
     * Enter description here ...
     *
     */
    function length() {
        return sizeof($this->references);
    }

    /**
     *
     * Enter description here ...
     *
     * @param Reference $reference
     */
    function addReference($reference) {
        if ($reference != null && is_object($reference) && $reference instanceof Reference) {
            $this->references[] = $reference;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $ref = "";
        if (sizeof($this->references) > 0) {
            $ref = "\n\t<guide>\n";
            foreach ($this->references as $reference) {
                /** @var $reference Reference */
                $ref .= $reference->finalize();
            }
            $ref .= "\t</guide>\n";
        }

        return $ref;
    }
}
