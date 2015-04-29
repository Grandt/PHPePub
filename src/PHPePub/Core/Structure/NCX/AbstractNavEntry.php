<?php
/**
 * Created by PhpStorm.
 * User: Grandt
 * Date: 03-08-14
 * Time: 17:28
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
