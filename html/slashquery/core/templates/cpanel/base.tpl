<?php

$this->filter = 1;

if ($this['uid']) {
  $this->block('header.tpl');
  $this->block($this->module_tpl);
  $this->block('footer.tpl');
} else {
  $this->block('login.tpl');
}
