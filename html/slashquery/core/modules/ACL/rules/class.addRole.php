<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/ACL/rules/class.addRole.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAddRole extends sqFinalRule {

  protected function evaluate() {

 		if (sqTools::postVars('role','rid')) {
      sqTools::jStatus( (new coreACL($this->router, $this->DB))->addRole(trim($_POST['role']), $_POST['rid']) );
		} else {
			sqTools::jStatus();
		}

  }

}
