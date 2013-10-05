<?php
/**
 * ePub OPF file structure
 *
 * @author A. Grandt <php@grandt.com>
 * @copyright 2009-2013 A. Grandt
 * @license GNU LGPL, Attribution required for commercial implementations, requested for everything else.
 * @version 1.00
 */
class Opf {
    const VERSION = 1.00;

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

    private $ident = "BookId";

    public $metadata = NULL;
    public $manifest = NULL;
    public $spine = NULL;
    public $guide = NULL;

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct($ident = "BookId") {
        $this->setIdent($ident);
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
        unset ($this->ident, $this->metadata, $this->manifest, $this->spine, $this->guide);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $ident
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
        $opf = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<package xmlns=\"http://www.idpf.org/2007/opf\" unique-identifier=\"" . $this->ident . "\" version=\"2.0\">\n";
        $opf .= $this->metadata->finalize();
        $opf .= $this->manifest->finalize();
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
     * @param unknown_type $title
     * @param unknown_type $language
     * @param unknown_type $identifier
     * @param unknown_type $identifierScheme
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
     * @param unknown_type $id
     * @param unknown_type $href
     * @param unknown_type $mediaType
     */
    function addItem($id, $href, $mediaType) {
        $this->manifest->addItem(new Item($id, $href, $mediaType));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $idref
     * @param unknown_type $linear
     */
    function addItemRef($idref, $linear = TRUE) {
        $this->spine->addItemref(new Itemref($idref, $linear));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $type
     * @param unknown_type $title
     * @param unknown_type $href
     */
    function addReference($type, $title, $href) {
        $this->guide->addReference(new Reference($type, $title, $href));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    function addDCMeta($name, $value) {
        $this->metadata->addDublinCore(new DublinCore($name, $value));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $content
     */
    function addMeta($name, $content) {
        $this->metadata->addMeta($name, $content);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $fileAs
     * @param unknown_type $role Use the MarcCode constants
     */
    function addCreator($name, $fileAs = NULL, $role = NULL) {
        $dc = new DublinCore(DublinCore::CREATOR, trim($name));

        if ($fileAs !== NULL) {
            $dc->addOpfAttr("file-as", trim($fileAs));
        }

        if ($role !== NULL) {
            $dc->addOpfAttr("role", trim($role));
        }

        $this->dc[] = $dc;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $fileAs
     * @param unknown_type $role Use the MarcCode constants
     */
    function addColaborator($name, $fileAs = NULL, $role = NULL) {
        $dc = new DublinCore(DublinCore::CONTRIBUTOR, trim($name));

        if ($fileAs !== NULL) {
            $dc->addOpfAttr("file-as", trim($fileAs));
        }

        if ($role !== NULL) {
            $dc->addOpfAttr("role", trim($role));
        }

        $this->dc[] = $dc;
    }
}

/**
 * ePub OPF Metadata structures
 */
class Metadata {
    const VERSION = 1.00;

    private $dc = array();
    private $meta = array();

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct() {
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->dc, $this->meta);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $dc
     */
    function addDublinCore($dc) {
        if ($dc != NULL && is_object($dc) && get_class($dc) === "DublinCore") {
            $this->dc[] = $dc;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $content
     */
    function addMeta($name, $content) {
        $name = is_string($name) ? trim($name) : NULL;
        if (isset($name)) {
            $content = is_string($content) ? trim($content) : NULL;
        }
        if (isset($content)) {
            $this->meta[] = array ($name => $content);
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $metadata = "\t<metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n\t\txmlns:opf=\"http://www.idpf.org/2007/opf\"\n\t\txmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";

        foreach ($this->dc as $dc) {
            $metadata .= $dc->finalize();
        }

        foreach ($this->meta as $data) {
            list($name, $content) = each($data);
            $metadata .= "\t\t<meta name=\"" . $name . "\" content=\"" . $content . "\" />\n";
        }

        return $metadata . "\t</metadata>\n";
    }
}

/**
 * ePub OPF Dublin Core (dc:) Metadata structures
 */
class DublinCore {
    const VERSION = 1.00;

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

    private $dcName = NULL;
    private $dcValue = NULL;
    private $attr = array();
    private $opfAttr = array();

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct($name, $value) {
        $this->setDc($name, $value);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->dcName, $this->dcValue, $this->attr, $this->opfAttr);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    function setDc($name, $value) {
        $this->dcName = is_string($name) ? trim($name) : NULL;
        if (isset($this->dcName)) {
            $this->dcValue = isset($value) ? (string)$value : NULL;
        }
        if (! isset($this->dcValue)) {
            $this->dcName = NULL;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $attrName
     * @param unknown_type $attrValue
     */
    function addAttr($attrName, $attrValue) {
        $attrName = is_string($attrName) ? trim($attrName) : NULL;
        if (isset($attrName)) {
            $attrValue = is_string($attrValue) ? trim($attrValue) : NULL;
        }
        if (isset($attrValue)) {
            $this->attr[$attrName] = $attrValue;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $opfAttrName
     * @param unknown_type $opfAttrValue
     */
    function addOpfAttr($opfAttrName, $opfAttrValue) {
        $opfAttrName = is_string($opfAttrName) ? trim($opfAttrName) : NULL;
        if (isset($opfAttrName)) {
            $opfAttrValue = is_string($opfAttrValue) ? trim($opfAttrValue) : NULL;
        }
        if (isset($opfAttrValue)) {
            $this->opfAttr[$opfAttrName] = $opfAttrValue;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $dc = "\t\t<dc:" . $this->dcName;

        if (sizeof($this->attr) > 0) {
            while (list($name, $content) = each($this->attr)) {
                $dc .= " " . $name . "=\"" . $content . "\"";
            }
        }

        if (sizeof($this->opfAttr) > 0) {
            while (list($name, $content) = each($this->opfAttr)) {
                $dc .= " opf:" . $name . "=\"" . $content . "\"";
            }
        }

        return $dc . ">" . $this->dcValue . "</dc:" . $this->dcName . ">\n";
    }
}

/**
 * ePub OPF Manifest structure
 */
class Manifest {
    const VERSION = 1.00;

    private $items = array();

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct() {
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->items);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $item
     */
    function addItem($item) {
        if ($item != NULL && is_object($item) && get_class($item) === "Item") {
            $this->items[] = $item;
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $manifest = "\n\t<manifest>\n";
        foreach ($this->items as $item) {
            $manifest .= $item->finalize();
        }
        return $manifest . "\t</manifest>\n";
    }
}

/**
 * ePub OPF Item structure
 */
class Item {
    const VERSION = 1.00;

    private $id = NULL;
    private $href = NULL;
    private $mediaType = NULL;
    private $requiredNamespace = NULL;
    private $requiredModules = NULL;
    private $fallback = NULL;
    private $fallbackStyle = NULL;

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct($id, $href, $mediaType) {
        $this->setId($id);
        $this->setHref($href);
        $this->setMediaType($mediaType);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->id, $this->href, $this->mediaType);
        unset ($this->requiredNamespace, $this->requiredModules, $this->fallback, $this->fallbackStyle);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $id
     */
    function setId($id) {
        $this->id = is_string($id) ? trim($id) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $href
     */
    function setHref($href) {
        $this->href = is_string($href) ? trim($href) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $mediaType
     */
    function setMediaType($mediaType) {
        $this->mediaType = is_string($mediaType) ? trim($mediaType) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $requiredNamespace
     */
    function setRequiredNamespace($requiredNamespace) {
        $this->requiredNamespace = is_string($requiredNamespace) ? trim($requiredNamespace) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $requiredModules
     */
    function setRequiredModules($requiredModules) {
        $this->requiredModules = is_string($requiredModules) ? trim($requiredModules) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $fallback
     */
    function setfallback($fallback) {
        $this->fallback = is_string($fallback) ? trim($fallback) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $fallbackStyle
     */
    function setFallbackStyle($fallbackStyle) {
        $this->fallbackStyle = is_string($fallbackStyle) ? trim($fallbackStyle) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $item = "\t\t<item id=\"" . $this->id . "\" href=\"" . $this->href . "\" media-type=\"" . $this->mediaType . "\" ";
        if (isset($this->requiredNamespace)) {
            $item .= "\n\t\t\trequired-namespace=\"" . $this->requiredNamespace . "\" ";
            if (isset($this->requiredModules)) {
                $item .= "required-modules=\"" . $this->requiredModules . "\" ";
            }
        }
        if (isset($this->fallback)) {
            $item .= "\n\t\t\tfallback=\"" . $this->fallback . "\" ";
        }
        if (isset($this->fallbackStyle)) {
            $item .= "\n\t\t\tfallback-style=\"" . $this->fallbackStyle . "\" ";
        }
        return $item . "/>\n";
    }
}

/**
 * ePub OPF Spine structure
 */
class Spine {
    const VERSION = 1.00;

    private $itemrefs = array();
    private $toc = NULL;

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct($toc = "ncx") {
        $this->setToc($toc);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->itemrefs, $this->toc);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $toc
     */
    function setToc($toc) {
        $this->toc = is_string($toc) ? trim($toc) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $itemref
     */
    function addItemref($itemref) {
        if ($itemref != NULL && is_object($itemref) && get_class($itemref) === "Itemref") {
            $this->itemrefs[] = $itemref;
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
            $spine .= $itemref->finalize();
        }
        return $spine . "\t</spine>\n";
    }
}

/**
 * ePub OPF ItemRef structure
 */
class Itemref {
    const VERSION = 1.00;

    private $idref = NULL;
    private $linear = TRUE;

    /**
     * Class constructor.
     *
     * @return void
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
        unset ($this->idref, $this->linear);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $idref
     */
    function setIdref($idref) {
        $this->idref = is_string($idref) ? trim($idref) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $linear
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
        if ($this->linear == FALSE) {
            return $itemref .= " linear=\"no\" />\n";
        }
        return $itemref . " />\n";
    }
}

/**
 * ePub OPF Guide structure
 */
class Guide {
    const VERSION = 1.00;

    private $references = array();

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct() {
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->references);
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
     * @param unknown_type $reference
     */
    function addReference($reference) {
        if ($reference != NULL && is_object($reference) && get_class($reference) === "Reference") {
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
                $ref .= $reference->finalize();
            }
            $ref .= "\t</guide>\n";
        }
        return $ref;
    }
}

/**
 * Reference constants
 */
class Reference {
    const VERSION = 1.00;

    /* REFERENCE types are derived from the "Chicago Manual of Style"
     */

    /** Acknowledgements page */
    const ACKNOWLEDGEMENTS = "acknowledgements";

    /** Bibliography page */
    const BIBLIOGRAPHY = "bibliography";

    /** Colophon page */
    const COLOPHON = "colophon";

    /** Copyright page */
    const COPYRIGHT_PAGE = "copyright-page";

    /** Dedication */
    const DEDICATION = "dedication";

    /** Epigraph */
    const EPIGRAPH = "epigraph";

    /** Foreword */
    const FOREWORD = "foreword";

    /** Glossary page */
    const GLOSSARY = "glossary";

    /** back-of-book style index */
    const INDEX = "index";

    /** List of illustrations */
    const LIST_OF_ILLUSTRATIONS = "loi";

    /** List of tables */
    const LIST_OF_TABLES = "lot";

    /** Notes page */
    const NOTES = "notes";

    /** Preface page */
    const PREFACE = "preface";

    /** Table of contents */
    const TABLE_OF_CONTENTS = "toc";

    /** Page with possibly title, author, publisher, and other metadata */
    const TITLE_PAGE = "title-page";

    /** First page of the book, ie. first page of the first chapter */
    const TEXT = "text";

    private $type = NULL;
    private $title = NULL;
    private $href = NULL;

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct($type, $title, $href) {
        $this->setType($type);
        $this->setTitle($title);
        $this->setHref($href);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset ($this->type, $this->title, $this->href);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $type
     */
    function setType($type) {
        $this->type = is_string($type) ? trim($type) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $title
     */
    function setTitle($title) {
        $this->title = is_string($title) ? trim($title) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $href
     */
    function setHref($href) {
        $this->href = is_string($href) ? trim($href) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        return "\t\t<reference type=\"" . $this->type . "\" title=\"" . $this->title . "\" href=\"" . $this->href . "\" />\n";
    }
}

/**
 * Common Marc codes.
 * Ref: http://www.loc.gov/marc/relators/
 */
class MarcCode {
    const VERSION = 1.00;

    /**
     * Adapter
     *
     * Use for a person who
     * 1) reworks a musical composition, usually for a different medium, or
     * 2) rewrites novels or stories for motion pictures or other audiovisual medium.
     */
    const ADAPTER = "adp";

    /**
     * Annotator
     *
     * Use for a person who writes manuscript annotations on a printed item.
     */
    const ANNOTATOR = "ann";

    /**
     * Arranger
     *
     * Use for a person who transcribes a musical composition, usually for a different
     *  medium from that of the original; in an arrangement the musical substance remains
     *  essentially unchanged.
     */
    const ARRANGER = "arr";

    /**
     * Artist
     *
     * Use for a person (e.g., a painter) who conceives, and perhaps also implements,
     *  an original graphic design or work of art, if specific codes (e.g., [egr],
     *  [etr]) are not desired. For book illustrators, prefer Illustrator [ill].
     */
    const ARTIST = "art";

    /**
     * Associated name
     *
     * Use as a general relator for a name associated with or found in an item or
     *  collection, or which cannot be determined to be that of a Former owner [fmo]
     *  or other designated relator indicative of provenance.
     */
    const ASSOCIATED_NAME = "asn";

    /**
     * Author
     *
     * Use for a person or corporate body chiefly responsible for the intellectual
     *  or artistic content of a work. This term may also be used when more than one
     *  person or body bears such responsibility.
     */
    const AUTHOR = "aut";

    /**
     * Author in quotations or text extracts
     *
     * Use for a person whose work is largely quoted or extracted in a works to which
     *  he or she did not contribute directly. Such quotations are found particularly
     *  in exhibition catalogs, collections of photographs, etc.
     */
    const AUTHOR_IN_QUOTES = "aqt";

    /**
     * Author of afterword, colophon, etc.
     *
     * Use for a person or corporate body responsible for an afterword, postface,
     *  colophon, etc. but who is not the chief author of a work.
     */
    const AUTHOR_OF_AFTERWORD = "aft";

    /**
     * Author of introduction, etc.
     *
     * Use for a person or corporate body responsible for an introduction, preface,
     *  foreword, or other critical matter, but who is not the chief author.
     */
    const AUTHOR_OF_INTRO = "aui";

    /**
     * Bibliographic antecedent
     *
     * Use for the author responsible for a work upon which the work represented by
     *  the catalog record is based. This can be appropriate for adaptations, sequels,
     *  continuations, indexes, etc.
     */
    const BIB_ANTECEDENT = "ant";

    /**
     * Book producer
     *
     * Use for the person or firm responsible for the production of books and other
     *  print media, if specific codes (e.g., [bkd], [egr], [tyd], [prt]) are not desired.
     */
    const BOOK_PRODUCER = "bkp";

    /**
     * Collaborator
     *
     * Use for a person or corporate body that takes a limited part in the elaboration
     *  of a work of another author or that brings complements (e.g., appendices, notes)
     *  to the work of another author.
     */
    const COLABORATOR = "clb";

    /**
     * Commentator
     *
     * Use for a person who provides interpretation, analysis, or a discussion of the
     *  subject matter on a recording, motion picture, or other audiovisual medium.
     *  Compiler [com] Use for a person who produces a work or publication by selecting
     *  and putting together material from the works of various persons or bodies.
     */
    const COMMENTATOR = "cmm";

    /**
     * Designer
     *
     * Use for a person or organization responsible for design if specific codes (e.g.,
     *  [bkd], [tyd]) are not desired.
     */
    const DESIGNER = "dsr";

    /**
     * Editor
     *
     * Use for a person who prepares for publication a work not primarily his/her own,
     *  such as by elucidating text, adding introductory or other critical matter, or
     *  technically directing an editorial staff.
     */
    const EDITORT = "edt";

    /**
     * Illustrator
     *
     * Use for the person who conceives, and perhaps also implements, a design or
     *  illustration, usually to accompany a written text.
     */
    const ILLUSTRATOR = "ill";

    /**
     * Lyricist
     *
     * Use for the writer of the text of a song.
     */
    const LYRICIST = "lyr";

    /**
     * Metadata contact
     *
     * Use for the person or organization primarily responsible for compiling and
     *  maintaining the original description of a metadata set (e.g., geospatial
     *  metadata set).
     */
    const METADATA_CONTACT = "mdc";

    /**
     * Musician
     *
     * Use for the person who performs music or contributes to the musical content
     *  of a work when it is not possible or desirable to identify the function more
     *  precisely.
     */
    const MUSICIAN = "mus";

    /**
     * Narrator
     *
     * Use for the speaker who relates the particulars of an act, occurrence, or
     *  course of events.
     */
    const NARRATOR = "nrt";

    /**
     * Other
     *
     * Use for relator codes from other lists which have no equivalent in the MARC
     *  list or for terms which have not been assigned a code.
     */
    const OTHER = "oth";

    /**
     * Photographer
     *
     * Use for the person or organization responsible for taking photographs, whether
     *  they are used in their original form or as reproductions.
     */
    const PHOTOGRAPHER = "pht";

    /**
     * Printer
     *
     * Use for the person or organization who prints texts, whether from type or plates.
     */
    const PRINTER = "prt";

    /**
     * Redactor
     *
     * Use for a person who writes or develops the framework for an item without
     *  being intellectually responsible for its content.
     */
    const REDACTOR = "red";

    /**
     * Reviewer
     *
     * Use for a person or corporate body responsible for the review of book, motion
     *  picture, performance, etc.
     */
    const REVIEWER = "rev";

    /**
     * Sponsor
     *
     * Use for the person or agency that issued a contract, or under whose auspices
     *  a work has been written, printed, published, etc.
     */
    const SPONSOR = "spn";

    /**
     * Thesis advisor
     *
     * Use for the person under whose supervision a degree candidate develops and
     *  presents a thesis, memoir, or text of a dissertation.
     */
    const THESIS_ADVISOR = "ths";

    /**
     * Transcriber
     *
     * Use for a person who prepares a handwritten or typewritten copy from original
     *  material, including from dictated or orally recorded material.
     */
    const TRANSCRIBER = "trc";

    /**
     * Translator
     *
     * Use for a person who renders a text from one language into another, or from
     *  an older form of a language into the modern form.
     */
    const TRANSLATOR = "trl";
}
?>