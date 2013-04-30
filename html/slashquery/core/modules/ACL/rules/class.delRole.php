<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/ACL/rules/class.delRole.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDelRole extends sqFinalRule {

  protected function evaluate() {

 		if (sqTools::postVars('rid','token')) {
      sqTools::jStatus( (new coreACL($this->router, $this->DB))->delRole($_POST['rid'], $_POST['token']) );
		} else {
			sqTools::jStatus();
		}

  }

}
