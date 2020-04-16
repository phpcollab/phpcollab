<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;
use Codeception\Module;

class Acceptance extends Module
{
    /**
     * @param $selector
     * @return bool|int
     *
     * Searches the dom for the $selector and returns the count
     */
    function countElements($selector)
    {
        try {
            return count($this->getModule('PhpBrowser')->_findElements($selector));
        } catch (ModuleException $e) {
            return false;
        }
    }
}
