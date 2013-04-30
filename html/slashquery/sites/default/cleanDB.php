<?php
/**
 * Small script to optimize/repair database tables
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

define('SQ_ROOT', __DIR__);
$sqSite = basename(SQ_ROOT);

require_once '../../config.php';
require_once 'config.php';
require_once '../../core/vendor/dalmp/dalmp.php';

$db = new DALMP(DSN);

foreach ($db->GetCol('SHOW TABLES') as $table) {
  $rs = $db->Execute("OPTIMIZE TABLE $table");
  echo "optimizing $table: $rs",PHP_EOL;
  $rs = $db->Execute("REPAIR TABLE $table QUICK");
  echo "repairing $table: $rs",PHP_EOL;
}
