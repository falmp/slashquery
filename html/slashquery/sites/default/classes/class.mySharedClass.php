<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');

/**
 * @see http://php.net/manual/en/language.oop5.basic.php
 */
class mySharedClass {
    // property declaration
    public $var = 'a default value';

    // method declaration
    public function displayVar() {
        echo $this->var;
    }
}
