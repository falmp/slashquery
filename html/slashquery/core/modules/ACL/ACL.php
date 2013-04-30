<?php
/**
 * cPanel - /slashquery/core/modules/ACL/ACL.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

/**
 * session start and check perms
 */
$this->checkPerms();

/**
 * only admins (usertype 3)
 */
if ($SQ->ACL()->usertype != 3) { $SQ->TPL()->showError = 1; $this->notify(); return; }

/**
 * dispatch the module
 */
try {
  sqFactory::Manufacture(__DIR__, $SQ, $this);
} catch (Exception $e) {
  echo $e;
}
