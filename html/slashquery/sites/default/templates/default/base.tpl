<?php

/**
 * remove extra spaces
 */
$this->filter = 1;

/**
 * enables cache
 */
$this->cache = 0;

$this->block('header.tpl');
/**
 * catch the modules for 1 hour ($block, $timeout=3600)
 * echo "flush_all" | nc 127.0.0.1 11211
 * echo "stats" | nc 127.0.0.1 11211
 */
$this->block($this->module_tpl);
$this->block('footer.tpl');
