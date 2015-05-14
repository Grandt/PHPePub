<?php
namespace PHPePub\Core\Structure\OPF;

/**
 * ePub OPF Spine structure
 */
class Spine {
    const _VERSION = 3.30;

    private $itemrefs = array();
    private $toc = null;

    /**
     * Class constructor.
     */
    function __construct($toc = "ncx") {
        $this->setToc($toc);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $toc
     */
    function setToc($toc) {
        $this->toc = is_string($toc) ? trim($toc) : null;
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->itemrefs, $this->toc);
    }

    /**
     *
     * Enter description here ...
     *
     * @param Itemref $itemref
     */
    function addItemref($itemref) {
        if ($itemref != null
            && is_object($itemref)
            && $itemref instanceof Itemref
            && !isset($this->itemrefs[$itemref->getIdref()])
        ) {
            $this->itemrefs[$itemref->getIdref()] = $itemref;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $spine = "\n\t<spine toc=\"" . $this->toc . "\">\n";
        foreach ($this->itemrefs as $itemref) {
            /** @var $itemref ItemRef */
            $spine .= $itemref->finalize();
        }

        return $spine . "\t</spine>\n";
    }
}
