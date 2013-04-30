<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.deleteAbuseID.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDeleteAbuseID extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('id')) {
      sqTools::jStatus ( sqAbuse::DelID($_POST['id']) );
    }

    sqTools::jStatus();

  }

}
