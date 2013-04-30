<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.checkEmail.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCheckEmail extends sqFinalRule {

  protected function evaluate() {

 		if (sqTools::postVars('email')) {
			sqTools::jStatus( $this->DB()->PGetOne('SELECT uid FROM sq_users WHERE email=?', $_POST['email']) ? false : true );
		} else {
			sqTools::jStatus();
		}

  }

}
