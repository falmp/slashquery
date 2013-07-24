<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.delOpenID.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDelOpenID extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('id','token') && sqSession::validToken($_POST['token'], 'token', false)) {
      sqTools::jStatus($this->DB()->PExecute('DELETE FROM sq_users_openids WHERE id=? and uid=?', $_POST['id'], sqSession::Get('uid')));
    } else {
      sqTools::jStatus();
    }

  }

}
