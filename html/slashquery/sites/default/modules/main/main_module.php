<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * default - /slashquery/sites/default/modules/main/main_module.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

$module_description = 'main module';
# ACL format
# id => array(name , visibility)
$module_ACL = array(
  1 => array('core', 0)
);
