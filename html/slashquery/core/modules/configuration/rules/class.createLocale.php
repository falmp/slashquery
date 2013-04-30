<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.createLocale.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCreateLocale extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('iso', 'language') && sqSession::vaildToken($_POST['token'], 'token', false)) {
      sqTools::jStatus( sqLocale::create($_POST['iso'], $_POST['language'], $this->DB()) );
    } else {
      sqTools::jStatus();
    }

  }

}
