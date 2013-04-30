<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.optimizeDB.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqOptimizeDB extends sqFinalRule {

  protected function evaluate() {

 		foreach ($this->DB()->GetCol("SHOW TABLES") as $table) {
      $this->DB()->Execute("OPTIMIZE TABLE $table");
    }
    sqTools::jStatus(true);

  }

}
