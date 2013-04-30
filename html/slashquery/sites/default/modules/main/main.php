<?php
/**
 * default - /slashquery/sites/default/modules/main.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

/**
 * session start and check the ACL
 */
#$this->checkPerms();

/**
 * dispatch the module
 */
try {
  sqFactory::Manufacture(__DIR__, $SQ, $this);
} catch (Exception $e) {
  echo $e;
}
