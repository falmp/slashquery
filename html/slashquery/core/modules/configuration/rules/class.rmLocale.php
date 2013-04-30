<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.rmLocale.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqrmLocale extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('iso') && sqSession::vaildToken($_POST['token'], 'token', false)) {
      sqTools::jStatus( sqLocale::delete($_POST['iso'], $this->DB()) );
    } else {
      sqTools::jStatus();
    }
  }

}
