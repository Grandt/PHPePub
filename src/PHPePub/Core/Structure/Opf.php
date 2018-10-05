<?php
namespace PHPePub\Core\Structure;

use com\grandt\BinStringStatic;
use PHPePub\Core\EPub;
use PHPePub\Core\Structure\OPF\DublinCore;
use PHPePub\Core\Structure\OPF\Guide;
use PHPePub\Core\Structure\OPF\Item;
use PHPePub\Core\Structure\OPF\Itemref;
use PHPePub\Core\Structure\OPF\Manifest;
use PHPePub\Core\Structure\OPF\Metadata;
use PHPePub\Core\Structure\OPF\MetaValue;
use PHPePub\Core\Structure\OPF\Reference;
use PHPePub\Core\Structure\OPF\Spine;

/**
 * ePub OPF file structure
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009- A. Grandt
 * @license   GNU LGPL, Attribution required for commercial implementations, requested for everything else.
 */
class Opf {
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

    /** @var $metadata Metadata */
    public $metadata = null;
    /** @var $manifest Manifest */
    public $manifest = null;
    /** @var $spine Spine */
    public $spine = null;
    /** @var $guide Guide */
    public $guide = null;

    public $namespaces = array("xsi"=>"http://www.w3.org/2001/XMLSchema-instance");
    public $prefixes = array();

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
        $metadata = $this->metadata->finalize($this->bookVersion, $this->date);

        foreach($this->metadata->namespaces as $ns => $nsuri) {
            $this->addNamespace($ns, $nsuri);
        }

        $opf = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
            . "<package xmlns=\"http://www.idpf.org/2007/opf\"\n";

        foreach($this->namespaces as $ns => $uri) {
            $opf .= "\txmlns:$ns=\"$uri\"\n";
        }

        if ($this->bookVersion === EPub::BOOK_VERSION_EPUB3 && count($this->prefixes) > 0) {
            $opf .= "\tprefix=\"";
            $addSpace = false;
            foreach ($this->prefixes as $name => $uri) {
                if ($addSpace) {
                    $opf .= " ";
                } else {
                    $addSpace = true;
                }
                $opf .= "$name: $uri";
            }
            $opf .= "\"\n";
        }

        $opf .= "\tunique-identifier=\"" . $this->ident . "\" version=\"" . $this->bookVersion . "\">\n";
        $opf .= $metadata;
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
     * @param string $nsName
     * @param string $nsURI
     */
    function addNamespace($nsName, $nsURI) {
        if (!array_key_exists($nsName, $this->namespaces)) {
            $this->namespaces[$nsName] = $nsURI;
        }
    }

    /**
     * @param string $name
     * @param string $URI
     */
    function addPrefix($name, $URI) {
        if (!array_key_exists($name, $this->prefixes)) {
            $this->prefixes[$name] = $URI;
        }
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
     * @param string $id
     *
     * @return bool|Item Item if the id is found, else FALSE
     */
    function getItemById($id) {
        /** @var Item $item */
        foreach ($this->manifest->getItems() as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param string $href
     *
     * @param bool $startsWith
     * @return bool|array|Item Item if the href is found, else FALSE. If $startsWith is true, the returned object will be an array if any are found.
     */
    function getItemByHref($href, $startsWith = false) {
        $rv = array();

        /** @var Item $item */
        foreach ($this->manifest->getItems() as $item) {
            if (!$startsWith && $item->getHref() == $href) {
                return $item;
            } elseif($startsWith && BinStringStatic::startsWith($item->getHref(), $href)) {
                $rv[] = $item;
            }
        }
        if (count($rv) > 0) {
            return $rv;
        }
        return false;
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
        $this->addMetaValue(new DublinCore($name, $value));
    }

    /**
     *
     * @param MetaValue $value
     */
    function addMetaValue($value) {
        $this->metadata->addDublinCore($value);
    }

    /**
     * Add a meta value to the metadata.
     *
     * Meta values in the metadata looks like:
     * <meta name="name" content="value" />
     *
     * @param string $name
     * @param string $content
     */
    function addMeta($name, $content) {
        $this->metadata->addMeta($name, $content);
    }

    /**
     * Add a Meta property value to the metadata
     *
     * Properties in the metadata looks like:
     *   <meta property="namespace:name">value</meta>
     *
     * Remember to add the namespace as well.
     *
     * @param string $name  property name, including the namespace declaration, ie. "dcterms:modified"
     * @param string $content
     */
    function addMetaProperty($name, $content) {
        $this->metadata->addMetaProperty($name, $content);
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
