<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.backup.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqBackup extends sqFinalRule {

  protected function evaluate() {

    /**
     * backup command stored on session, so lets get it and do the work
     */
    sqTools::jStatus ( (system(sqSession::Get('backup')) === false) ? false : true  );

  }

}
