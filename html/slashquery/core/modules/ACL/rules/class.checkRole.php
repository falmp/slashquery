<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/ACL/rules/class.checkRole.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCheckRole extends sqFinalRule {

  protected function evaluate() {

 		if (sqTools::postVars('role', 'rid')) {
			$rs = ($_POST['rid'] > 0) ? $this->DB()->PGetOne('SELECT 1 FROM sq_roles WHERE name=? AND rid !=?', trim($_POST['role']), trim($_POST['rid'])) : $this->DB()->PGetOne('SELECT 1 FROM sq_roles WHERE name=?', trim($_POST['role']));
			sqTools::jStatus ($rs ? false : true);
		} else {
			sqTools::jStatus();
		}

  }

}
