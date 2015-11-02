<?php
/**
/**
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2014- A. Grandt
 * @license   GNU LGPL 2.1
 */

namespace PHPePub\Core\Structure\NCX;

abstract class AbstractNavEntry {
    /**
     * @return AbstractNavEntry
     */
    abstract public function getParent();

    /**
     * @return int level
     */
    abstract public function getLevel();

    abstract public function addNavPoint($navPoint);
}
