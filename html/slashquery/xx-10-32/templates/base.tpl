<?php

$this->filter = 1;

$this->block('header.tpl');
$this->block($this['code']);
$this->block('footer.tpl');
