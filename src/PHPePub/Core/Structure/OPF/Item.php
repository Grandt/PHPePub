<?php
namespace PHPePub\Core\Structure\OPF;
use PHPePub\Core\EPub;

/**
 * ePub OPF Item structure
 */
class Item {
    const _VERSION = 3.30;

    private $id = NULL;
    private $href = NULL;
    private $mediaType = NULL;
    private $properties = NULL;
    private $requiredNamespace = NULL;
    private $requiredModules = NULL;
    private $fallback = NULL;
    private $fallbackStyle = NULL;

    /**
     * Class constructor.
     *
     * @param $id
     * @param $href
     * @param $mediaType
     * @param null $properties
     */
    function __construct($id, $href, $mediaType, $properties = NULL) {
        $this->setId($id);
        $this->setHref($href);
        $this->setMediaType($mediaType);
        $this->setProperties($properties);
    }

    /**
     * Class destructor
     *
     * @return void
     */
    function __destruct() {
        unset($this->id, $this->href, $this->mediaType);
        unset($this->properties, $this->requiredNamespace, $this->requiredModules, $this->fallback, $this->fallbackStyle);
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $id
     */
    function setId($id) {
        $this->id = is_string($id) ? trim($id) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $href
     */
    function setHref($href) {
        $this->href = is_string($href) ? trim($href) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $mediaType
     */
    function setMediaType($mediaType) {
        $this->mediaType = is_string($mediaType) ? trim($mediaType) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $properties
     */
    function setProperties($properties) {
        $this->properties = is_string($properties) ? trim($properties) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $requiredNamespace
     */
    function setRequiredNamespace($requiredNamespace) {
        $this->requiredNamespace = is_string($requiredNamespace) ? trim($requiredNamespace) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $requiredModules
     */
    function setRequiredModules($requiredModules) {
        $this->requiredModules = is_string($requiredModules) ? trim($requiredModules) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $fallback
     */
    function setfallback($fallback) {
        $this->fallback = is_string($fallback) ? trim($fallback) : NULL;
    }

    /**
     *
     * Enter description here ...
     *
     * @param string $fallbackStyle
     */
    function setFallbackStyle($fallbackStyle) {
        $this->fallbackStyle = is_string($fallbackStyle) ? trim($fallbackStyle) : NULL;
    }

    /**
     *
     * @param string $bookVersion
     * @return string
     */
    function finalize($bookVersion = EPub::BOOK_VERSION_EPUB2) {
        $item = "\t\t<item id=\"" . $this->id . "\" href=\"" . $this->href . "\" media-type=\"" . $this->mediaType . "\" ";
        if ($bookVersion === EPub::BOOK_VERSION_EPUB3 && isset($this->properties)) {
            $item .= "properties=\"" . $this->properties . "\" ";
        }
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
