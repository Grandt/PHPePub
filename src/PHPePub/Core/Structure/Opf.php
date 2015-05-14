<?php
namespace PHPePub\Core\Structure;

use PHPePub\Core\EPub;
use PHPePub\Core\Structure\OPF\DublinCore;
use PHPePub\Core\Structure\OPF\Guide;
use PHPePub\Core\Structure\OPF\Item;
use PHPePub\Core\Structure\OPF\Itemref;
use PHPePub\Core\Structure\OPF\Manifest;
use PHPePub\Core\Structure\OPF\Reference;
use PHPePub\Core\Structure\OPF\Spine;

/**
 * ePub OPF file structure
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009-2014 A. Grandt
 * @license   GNU LGPL, Attribution required for commercial implementations, requested for everything else.
 * @version   3.30
 */
class Opf {
    const _VERSION = 3.30;

    /* Core Media types.
     * These types are the only guaranteed mime types any ePub reader must understand.
     * Any other type muse define a fall back whose fallback chain will end in one of these.
     */
    const TYPE_GIF = "image/gif";
    const TYPE_JPEG = "image/jpeg";
    const TYPE_PNG = "image/png";
    const TYPE_SVG = "image/svg+xml";
    const TYPE_XHTML = "application/xhtml+xml";
    const TYPE_DTBOOK = "application/x-dtbook+xml";
    const TYPE_CSS = "text/css";
    const TYPE_XML = "application/xml";
    const TYPE_OEB1_DOC = "text/x-oeb1-document"; // Deprecated
    const TYPE_OEB1_CSS = "text/x-oeb1-css"; // Deprecated
    const TYPE_NCX = "application/x-dtbncx+xml";

    private $bookVersion = EPub::BOOK_VERSION_EPUB2;
    private $ident = "BookId";

    public $date = null;
    public $metadata = null;
    public $manifest = null;
    public $spine = null;
    public $guide = null;

    /**
     * Class constructor.
     *
     * @param string $ident
     * @param string $bookVersion
     */
    function __construct($ident = "BookId", $bookVersion = EPub::BOOK_VERSION_EPUB2) {
        $this->setIdent($ident);
        $this->setVersion($bookVersion);
        $this->metadata = new Metadata();
        $this->manifest = new Manifest();
        $this->spine = new Spine();
        $this->guide = new Guide();
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->bookVersion, $this->ident, $this->date, $this->metadata, $this->manifest, $this->spine, $this->guide);
    }

    /**
     *
     * Enter description here ...
     *
     * @param $bookVersion
     */
    function setVersion($bookVersion) {
        $this->bookVersion = is_string($bookVersion) ? trim($bookVersion) : EPub::BOOK_VERSION_EPUB2;
    }

    function isEPubVersion2() {
        return $this->bookVersion === EPub::BOOK_VERSION_EPUB2;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $ident
     */
    function setIdent($ident = "BookId") {
        $this->ident = is_string($ident) ? trim($ident) : "BookId";
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $opf = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
            . "<package xmlns=\"http://www.idpf.org/2007/opf\" unique-identifier=\"" . $this->ident . "\" version=\"" . $this->bookVersion . "\">\n";

        $opf .= $this->metadata->finalize($this->bookVersion, $this->date);
        $opf .= $this->manifest->finalize($this->bookVersion);
        $opf .= $this->spine->finalize();

        if ($this->guide->length() > 0) {
            $opf .= $this->guide->finalize();
        }

        return $opf . "</package>\n";
    }

    // Convenience functions:

    /**
     *
     * Enter description here ...
     *
     * @param string $title
     * @param string $language
     * @param string $identifier
     * @param string $identifierScheme
     */
    function initialize($title, $language, $identifier, $identifierScheme) {
        $this->metadata->addDublinCore(new DublinCore("title", $title));
        $this->metadata->addDublinCore(new DublinCore("language", $language));

        $dc = new DublinCore("identifier", $identifier);
        $dc->addAttr("id", $this->ident);
        $dc->addOpfAttr("scheme", $identifierScheme);
        $this->metadata->addDublinCore($dc);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $id
     * @param string $href
     * @param string $mediaType
     * @param string $properties
     */
    function addItem($id, $href, $mediaType, $properties = null) {
        $this->manifest->addItem(new Item($id, $href, $mediaType, $properties));
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $idref
     * @param bool   $linear
     */
    function addItemRef($idref, $linear = true) {
        $this->spine->addItemref(new Itemref($idref, $linear));
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $type
     * @param string $title
     * @param string $href
     */
    function addReference($type, $title, $href) {
        $this->guide->addReference(new Reference($type, $title, $href));
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $value
     */
    function addDCMeta($name, $value) {
        $this->metadata->addDublinCore(new DublinCore($name, $value));
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $content
     */
    function addMeta($name, $content) {
        $this->metadata->addMeta($name, $content);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $fileAs
     * @param string $role Use the MarcCode constants
     */
    function addCreator($name, $fileAs = null, $role = null) {
        $dc = new DublinCore(DublinCore::CREATOR, trim($name));

        if ($fileAs !== null) {
            $dc->addOpfAttr("file-as", trim($fileAs));
        }

        if ($role !== null) {
            $dc->addOpfAttr("role", trim($role));
        }

        $this->metadata->addDublinCore($dc);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $fileAs
     * @param string $role Use the MarcCode constants
     */
    function addColaborator($name, $fileAs = null, $role = null) {
        $dc = new DublinCore(DublinCore::CONTRIBUTOR, trim($name));

        if ($fileAs !== null) {
            $dc->addOpfAttr("file-as", trim($fileAs));
        }

        if ($role !== null) {
            $dc->addOpfAttr("role", trim($role));
        }

        $this->metadata->addDublinCore($dc);
    }
}

/**
 * ePub OPF Metadata structures
 */
class Metadata {
    const _VERSION = 3.30;

    private $dc = array();
    private $meta = array();

    /**
     * Class constructor.
     *
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

        /** @var $dc DublinCore */
        foreach ($this->dc as $dc) {
            $metadata .= $dc->finalize($bookVersion);
        }

        foreach ($this->meta as $data) {
            list($name, $content) = each($data);
            $metadata .= "\t\t<meta name=\"" . $name . "\" content=\"" . $content . "\" />\n";
        }

        return $metadata . "\t</metadata>\n";
    }
}
