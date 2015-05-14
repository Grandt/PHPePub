<?php
namespace PHPePub\Core\Structure\OPF;

use PHPePub\Core\EPub;

/**
 * ePub OPF Metadata structures
 */
class Metadata {
    const _VERSION = 3.30;

    private $dc = array();
    private $meta = array();

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
     * @param DublinCore $dc
     */
    function addDublinCore($dc) {
        if ($dc != null && is_object($dc) && $dc instanceof DublinCore) {
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
     * @param string $bookVersion
     * @param int    $date
     *
     * @return string
     */
    function finalize($bookVersion = EPub::BOOK_VERSION_EPUB2, $date = null) {
        $metadata = "\t<metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
        if ($bookVersion === EPub::BOOK_VERSION_EPUB2) {
            $metadata .= "\t\txmlns:opf=\"http://www.idpf.org/2007/opf\"\n\t\txmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
        } else {
            $metadata .= "\t\txmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
            if (!isset($date)) {
                $date = time();
            }
            $metadata .= "\t\t<meta property=\"dcterms:modified\">" . gmdate("Y-m-d\TH:i:s\Z", $date) . "</meta>\n";
        }

        foreach ($this->dc as $dc) {
            /** @var $dc DublinCore */
            $metadata .= $dc->finalize($bookVersion);
        }

        foreach ($this->meta as $data) {
            list($name, $content) = each($data);
            $metadata .= "\t\t<meta name=\"" . $name . "\" content=\"" . $content . "\" />\n";
        }

        return $metadata . "\t</metadata>\n";
    }
}
