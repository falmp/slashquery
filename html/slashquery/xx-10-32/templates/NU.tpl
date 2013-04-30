<?php

switch ($this['status']) {
  case 'expired':
    $this->block('expired.tpl');
    break;

  case 'confirmed':
    $this->block('confirmed.tpl');
    break;

  case 'invalid':
    $this->block('invalid.tpl');
    break;
}
