<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.checkOpenID.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCheckOpenID extends sqFinalRule {

  protected function evaluate() {

 		if (sqTools::postVars('oi')) {
      /**
       * check if openid domains exists
       */
			sqTools::jStatus( (new sqOpenID())->setIdentity($_POST['oi'])->Discover() ? true : false);
		} else {
			sqTools::jStatus();
		}

  }

}
