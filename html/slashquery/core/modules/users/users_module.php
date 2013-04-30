<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * cPanel - /slashquery/core/modules/users/users_module.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

$module_description = 'core User management';

/**
 * ACL format
 * id => array(name)
 */
$module_ACL = array(
  1 => array('view'),
  2 => array('create'),
  3 => array('edit'),
  4 => array('delete')
);
