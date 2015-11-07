<?php
namespace PHPePub\Core\Structure;

use PHPePub\Core\EPub;
use PHPePub\Core\Structure\NCX\NavMap;
use PHPePub\Core\Structure\NCX\NavPoint;

/**
 * ePub NCX file structure
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009- A. Grandt
 * @license   GNU LGPL, Attribution required for commercial implementations, requested for everything else.
 */
class Ncx {
    const MIMETYPE = "application/x-dtbncx+xml";

    private $bookVersion = EPub::BOOK_VERSION_EPUB2;

    /** @var EPub $parentBook */
    private $parentBook = null;

    private $navMap = null;
    private $uid = null;
    private $meta = array();
    private $docTitle = null;
    private $docAuthor = null;

    private $currentLevel = null;
    private $lastLevel = null;

    private $languageCode = "en";
    private $writingDirection = EPub::DIRECTION_LEFT_TO_RIGHT;

    public $chapterList = array();
    public $referencesTitle = "Guide";
    public $referencesClass = "references";
    public $referencesId = "references";
    public $referencesList = array();
    public $referencesName = array();
    public $referencesOrder = null;

    /**
     * Class constructor.
     *
     * @param string $uid
     * @param string $docTitle
     * @param string $docAuthor
     * @param string $languageCode
     * @param string $writingDirection
     */
    function __construct($uid = null, $docTitle = null, $docAuthor = null, $languageCode = "en", $writingDirection = EPub::DIRECTION_LEFT_TO_RIGHT) {
        $this->navMap = new NavMap($writingDirection);
        $this->currentLevel = $this->navMap;
        $this->setUid($uid);
        $this->setDocTitle($docTitle);
        $this->setDocAuthor($docAuthor);
        $this->setLanguageCode($languageCode);
        $this->setWritingDirection($writingDirection);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->parentBook, $this->bookVersion, $this->navMap, $this->uid, $this->meta);
        unset($this->docTitle, $this->docAuthor, $this->currentLevel, $this->lastLevel);
        unset($this->languageCode, $this->writingDirection, $this->chapterList, $this->referencesTitle);
        unset($this->referencesClass, $this->referencesId, $this->referencesList, $this->referencesName);
        unset($this->referencesOrder);
    }

    /**
     * @param EPub $parentBook
     */
    public function setBook($parentBook) {
        $this->parentBook = $parentBook;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $bookVersion
     */
    function setVersion($bookVersion) {
        $this->bookVersion = is_string($bookVersion) ? trim($bookVersion) : EPub::BOOK_VERSION_EPUB2;
    }

    /**
     *
     * @return bool TRUE if the book is set to type ePub 2
     */
    function isEPubVersion2() {
        return $this->bookVersion === EPub::BOOK_VERSION_EPUB2;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $uid
     */
    function setUid($uid) {
        $this->uid = is_string($uid) ? trim($uid) : null;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $docTitle
     */
    function setDocTitle($docTitle) {
        $this->docTitle = is_string($docTitle) ? trim($docTitle) : null;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $docAuthor
     */
    function setDocAuthor($docAuthor) {
        $this->docAuthor = is_string($docAuthor) ? trim($docAuthor) : null;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $languageCode
     */
    function setLanguageCode($languageCode) {
        $this->languageCode = is_string($languageCode) ? trim($languageCode) : "en";
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $writingDirection
     */
    function setWritingDirection($writingDirection) {
        $this->writingDirection = is_string($writingDirection) ? trim($writingDirection) : EPub::DIRECTION_LEFT_TO_RIGHT;
    }

    /**
     *
     * Enter description here ...
     *
     * @param NavMap $navMap
     */
    function setNavMap($navMap) {
        if ($navMap != null && is_object($navMap) && $navMap instanceof NavMap) {
            $this->navMap = $navMap;
        }
    }

    /**
     * Add one chapter level.
     *
     * Subsequent chapters will be added to this level.
     *
     * @param string $navTitle
     * @param string $navId
     * @param string $navClass
     * @param bool   $isNavHidden
     * @param null   $writingDirection
     *
     * @return bool|NavPoint
     */
    function subLevel($navTitle = null, $navId = null, $navClass = null, $isNavHidden = false, $writingDirection = null) {
        $navPoint = false;
        if (isset($navTitle) && isset($navClass)) {
            $navPoint = new NavPoint($navTitle, null, $navId, $navClass, $isNavHidden, $writingDirection);
            $this->addNavPoint($navPoint);
        }
        if ($this->lastLevel !== null) {
            $this->currentLevel = $this->lastLevel;
        }

        return $navPoint;
    }

    /**
     * Step back one chapter level.
     *
     * Subsequent chapters will be added to this chapters parent level.
     */
    function backLevel() {
        $this->lastLevel = $this->currentLevel;
        $this->currentLevel = $this->currentLevel->getParent();
    }

    /**
     * Step back to the root level.
     *
     * Subsequent chapters will be added to the rooot NavMap.
     */
    function rootLevel() {
        $this->lastLevel = $this->currentLevel;
        $this->currentLevel = $this->navMap;
    }

    /**
     * Step back to the given level.
     * Useful for returning to a previous level from deep within the structure.
     * Values below 2 will have the same effect as rootLevel()
     *
     * @param int $newLevel
     */
    function setCurrentLevel($newLevel) {
        if ($newLevel <= 1) {
            $this->rootLevel();
        } else {
            while ($this->currentLevel->getLevel() > $newLevel) {
                $this->backLevel();
            }
        }
    }

    /**
     * Get current level count.
     * The indentation of the current structure point.
     *
     * @return int current level count;
     */
    function getCurrentLevel() {
        return $this->currentLevel->getLevel();
    }

    /**
     * Add child NavPoints to current level.
     *
     * @param NavPoint $navPoint
     */
    function addNavPoint($navPoint) {
        $this->lastLevel = $this->currentLevel->addNavPoint($navPoint);
    }

    /**
     *
     * Enter description here ...
     *
     * @return NavMap
     */
    function getNavMap() {
        return $this->navMap;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $name
     * @param string $content
     */
    function addMetaEntry($name, $content) {
        $name = is_string($name) ? trim($name) : null;
        $content = is_string($content) ? trim($content) : null;

        if ($name != null && $content != null) {
            $this->meta[] = array(
                $name => $content
            );
        }
    }

    /**
     *
     * Enter description here ...
     *
     * @return string
     */
    function finalize() {
        $nav = $this->navMap->finalize();

        $ncx = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        if ($this->isEPubVersion2()) {
            $ncx .= "<!DOCTYPE ncx PUBLIC \"-//NISO//DTD ncx 2005-1//EN\"\n"
                . "  \"http://www.daisy.org/z3986/2005/ncx-2005-1.dtd\">\n";
        }
        $ncx .= "<ncx xmlns=\"http://www.daisy.org/z3986/2005/ncx/\" version=\"2005-1\" xml:lang=\"" . $this->languageCode . "\" dir=\"" . $this->writingDirection . "\">\n"
            . "\t<head>\n"
            . "\t\t<meta name=\"dtb:uid\" content=\"" . $this->uid . "\" />\n"
            . "\t\t<meta name=\"dtb:depth\" content=\"" . $this->navMap->getNavLevels() . "\" />\n"
            . "\t\t<meta name=\"dtb:totalPageCount\" content=\"0\" />\n"
            . "\t\t<meta name=\"dtb:maxPageNumber\" content=\"0\" />\n";

        if (sizeof($this->meta)) {
            foreach ($this->meta as $metaEntry) {
                list($name, $content) = each($metaEntry);
                $ncx .= "\t\t<meta name=\"" . $name . "\" content=\"" . $content . "\" />\n";
            }
        }

        $ncx .= "\t</head>\n\n\t<docTitle>\n\t\t<text>"
            . $this->docTitle
            . "</text>\n\t</docTitle>\n\n\t<docAuthor>\n\t\t<text>"
            . $this->docAuthor
            . "</text>\n\t</docAuthor>\n\n"
            . $nav;

        return $ncx . "</ncx>\n";
    }

    /**
     *
     * @param string $title
     * @param string $cssFileName
     *
     * @return string
     */
    function finalizeEPub3($title = "Table of Contents", $cssFileName = null) {
        $end = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<html xmlns=\"http://www.w3.org/1999/xhtml\"\n"
            . "      xmlns:epub=\"http://www.idpf.org/2007/ops\"\n"
            . "      xml:lang=\"" . $this->languageCode . "\" lang=\"" . $this->languageCode . "\" dir=\"" . $this->writingDirection . "\">\n"
            . "\t<head>\n"
            . "\t\t<title>" . $this->docTitle . "</title>\n"
            . "\t\t<meta http-equiv=\"default-style\" content=\"text/html; charset=utf-8\"/>\n";

        if ($this->parentBook !== null) {
            $end .= $this->parentBook->getViewportMetaLine();
        }

        if ($cssFileName !== null) {
            $end .= "\t\t<link rel=\"stylesheet\" href=\"" . $cssFileName . "\" type=\"text/css\"/>\n";
        }

        $end .= "\t</head>\n"
            . "\t<body epub:type=\"frontmatter toc\">\n"
            . "\t\t<header>\n"
            . "\t\t\t<h1>" . $title . "</h1>\n"
            . "\t\t</header>\n"
            . $this->navMap->finalizeEPub3()
            . $this->finalizeEPub3Landmarks()
            . "\t</body>\n"
            . "</html>\n";

        return $end;
    }

    /**
     * Build the references for the ePub 2 toc.
     * These are merely reference pages added to the end of the navMap though.
     *
     * @return string
     */
    function finalizeReferences() {
        if (isset($this->referencesList) && sizeof($this->referencesList) > 0) {
            $this->rootLevel();
            $this->subLevel($this->referencesTitle, $this->referencesId, $this->referencesClass);
            $refId = 1;
            while (list($item, $descriptive) = each($this->referencesOrder)) {
                if (array_key_exists($item, $this->referencesList)) {
                    $name = (empty($this->referencesName[$item]) ? $descriptive : $this->referencesName[$item]);
                    $navPoint = new NavPoint($name, $this->referencesList[$item], "ref-" . $refId++);
                    $this->addNavPoint($navPoint);
                }
            }
        }
    }

    /**
     * Build the landmarks for the ePub 3 toc.
     *
     * @return string
     */
    function finalizeEPub3Landmarks() {
        $lm = "";
        if (isset($this->referencesList) && sizeof($this->referencesList) > 0) {
            $lm = "\t\t\t<nav epub:type=\"landmarks\">\n"
                . "\t\t\t\t<h2"
                . ($this->writingDirection === EPub::DIRECTION_RIGHT_TO_LEFT ? " dir=\"rtl\"" : "") . ">"
                . $this->referencesTitle . "</h2>\n"
                . "\t\t\t\t<ol>\n";

            $li = "";
            while (list($item, $descriptive) = each($this->referencesOrder)) {
                if (array_key_exists($item, $this->referencesList)) {
                    $li .= "\t\t\t\t\t<li><a epub:type=\""
                        . $item
                        . "\" href=\"" . $this->referencesList[$item] . "\">"
                        . (empty($this->referencesName[$item]) ? $descriptive : $this->referencesName[$item])
                        . "</a></li>\n";
                }
            }
            if (empty($li)) {
                return "";
            }

            $lm .= $li
                . "\t\t\t\t</ol>\n"
                . "\t\t\t</nav>\n";
        }

        return $lm;
    }
}
