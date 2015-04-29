<?php

namespace PHPePub\Core\Structure\NCX;

use PHPePub\Core\EPub;

/**
 * ePub NavPoint class
 */
class NavPoint extends AbstractNavEntry {
    const _VERSION = 3.30;

    private $label = null;
    private $contentSrc = null;
    private $id = null;
    private $navClass = null;
    private $isNavHidden = false;
    private $navPoints = array();
    /** @var $parent AbstractNavEntry */
    private $parent = null;
    private $writingDirection = EPub::DIRECTION_LEFT_TO_RIGHT;

    /**
     * Class constructor.
     *
     * All three attributes are mandatory, though if ID is set to null (default) the value will be generated.
     *
     * @param string $label
     * @param string $contentSrc
     * @param string $id
     * @param string $navClass
     * @param bool   $isNavHidden
     * @param string $writingDirection
     */
    function __construct($label, $contentSrc = null, $id = null, $navClass = null, $isNavHidden = false, $writingDirection = null) {
        $this->setLabel($label);
        $this->setContentSrc($contentSrc);
        $this->setId($id);
        $this->setNavClass($navClass);
        $this->setNavHidden($isNavHidden);
        $this->setWritingDirection($writingDirection);
    }

    /**
     * Set the id for the NavPoint.
     *
     * The id must be unique, and is mandatory.
     *
     * @param string $id
     */
    function setId($id) {
        $this->id = is_string($id) ? trim($id) : null;
    }

    /**
     * Set the class to be used for this NavPoint.
     *
     * @param string $navClass
     */
    function setNavClass($navClass) {
        $this->navClass = isset($navClass) && is_string($navClass) ? trim($navClass) : null;
    }

    /**
     * Set the class to be used for this NavPoint.
     *
     * @param $isNavHidden
     */
    function setNavHidden($isNavHidden) {
        $this->isNavHidden = $isNavHidden === true;
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->label, $this->contentSrc, $this->id, $this->navClass);
        unset($this->isNavHidden, $this->navPoints, $this->parent);
    }

    /**
     * Get the Text label for the NavPoint.
     *
     * @return string Label
     */
    function getLabel() {
        return $this->label;
    }

    /**
     * Set the Text label for the NavPoint.
     *
     * The label is mandatory.
     *
     * @param string $label
     */
    function setLabel($label) {
        $this->label = is_string($label) ? trim($label) : null;
    }

    /**
     * Get the src reference for the NavPoint.
     *
     * @return string content src url.
     */
    function getContentSrc() {
        return $this->contentSrc;
    }

    /**
     * Set the src reference for the NavPoint.
     *
     * The src is mandatory for ePub 2.
     *
     * @param string $contentSrc
     */
    function setContentSrc($contentSrc) {
        $this->contentSrc = isset($contentSrc) && is_string($contentSrc) ? trim($contentSrc) : null;
    }

    /**
     * Get the parent to this NavPoint.
     *
     * @return AbstractNavEntry if the parent is the root.
     */
    function getParent() {
        return $this->parent;
    }

    /**
     * Set the parent for this NavPoint.
     *
     * @param NavPoint|NavMap $parent
     */
    function setParent($parent) {
        if ($parent != null && is_object($parent) && $parent instanceof AbstractNavEntry) {
            $this->parent = $parent;
        }
    }

    /**
     * Get the current level. 1 = document root.
     *
     * @return int level
     */
    function getLevel() {
        return $this->parent === null ? 1 : $this->parent->getLevel() + 1;
    }

    /**
     * Add child NavPoints for multi level NavMaps.
     *
     * @param $navPoint
     *
     * @return $this
     */
    function addNavPoint($navPoint) {
        if ($navPoint != null && is_object($navPoint) && $navPoint instanceof NavPoint) {
            /** @var $navPoint NavPoint */
            $navPoint->setParent($this);
            if ($navPoint->getWritingDirection() == null) {
                $navPoint->setWritingDirection($this->writingDirection);
            }
            $this->navPoints[] = $navPoint;

            return $navPoint;
        }

        return $this;
    }

    function getWritingDirection() {
        return $this->writingDirection;
    }

    /**
     * Set the writing direction to be used for this NavPoint.
     *
     * @param string $writingDirection
     */
    function setWritingDirection($writingDirection) {
        $this->writingDirection = isset($writingDirection) && is_string($writingDirection) ? trim($writingDirection) : null;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $nav
     * @param int    $playOrder
     * @param int    $level
     *
     * @return int
     */
    function finalize(&$nav = "", &$playOrder = 0, $level = 0) {
        $maxLevel = $level;
        $levelAdjust = 0;

        if ($this->isNavHidden) {
            return $maxLevel;
        }

        if (isset($this->contentSrc)) {
            $playOrder++;

            if ($this->id == null) {
                $this->id = "navpoint-" . $playOrder;
            }
            $nav .= str_repeat("\t", $level) . "\t\t<navPoint id=\"" . $this->id . "\" playOrder=\"" . $playOrder . "\">\n"
                . str_repeat("\t", $level) . "\t\t\t<navLabel>\n"
                . str_repeat("\t", $level) . "\t\t\t\t<text>" . $this->label . "</text>\n"
                . str_repeat("\t", $level) . "\t\t\t</navLabel>\n"
                . str_repeat("\t", $level) . "\t\t\t<content src=\"" . $this->contentSrc . "\" />\n";
        } else {
            $levelAdjust++;
        }

        if (sizeof($this->navPoints) > 0) {
            $maxLevel++;
            foreach ($this->navPoints as $navPoint) {
                /** @var $navPoint NavPoint */
                $retLevel = $navPoint->finalize($nav, $playOrder, ($level + 1 + $levelAdjust));
                if ($retLevel > $maxLevel) {
                    $maxLevel = $retLevel;
                }
            }
        }

        if (isset($this->contentSrc)) {
            $nav .= str_repeat("\t", $level) . "\t\t</navPoint>\n";
        }

        return $maxLevel;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $nav
     * @param int    $playOrder
     * @param int    $level
     * @param null   $subLevelClass
     * @param bool   $subLevelHidden
     *
     * @return int
     */
    function finalizeEPub3(&$nav = "", &$playOrder = 0, $level = 0, $subLevelClass = null, $subLevelHidden = false) {
        $maxLevel = $level;

        if ($this->id == null) {
            $this->id = "navpoint-" . $playOrder;
        }
        $indent = str_repeat("\t", $level) . "\t\t\t\t";

        $nav .= $indent . "<li id=\"" . $this->id . "\"";
        if (isset($this->writingDirection)) {
            $nav .= " dir=\"" . $this->writingDirection . "\"";
        }
        $nav .= ">\n";

        if (isset($this->contentSrc)) {
            $nav .= $indent . "\t<a href=\"" . $this->contentSrc . "\">" . $this->label . "</a>\n";
        } else {
            $nav .= $indent . "\t<span>" . $this->label . "</span>\n";
        }

        if (sizeof($this->navPoints) > 0) {
            $maxLevel++;

            $nav .= $indent . "\t<ol epub:type=\"list\"";
            if (isset($subLevelClass)) {
                $nav .= " class=\"" . $subLevelClass . "\"";
            }
            if ($subLevelHidden) {
                $nav .= " hidden=\"hidden\"";
            }
            $nav .= ">\n";

            foreach ($this->navPoints as $navPoint) {
                /** @var $navPoint NavPoint */
                $retLevel = $navPoint->finalizeEPub3($nav, $playOrder, ($level + 2), $subLevelClass, $subLevelHidden);
                if ($retLevel > $maxLevel) {
                    $maxLevel = $retLevel;
                }
            }
            $nav .= $indent . "\t</ol>\n";
        }

        $nav .= $indent . "</li>\n";

        return $maxLevel;
    }
}
