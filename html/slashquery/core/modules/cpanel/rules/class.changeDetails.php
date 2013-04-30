<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.changeDetails.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqChangeDetails extends sqFinalRule {

  protected function evaluate() {

    /**
     * normal auth using email, password, token
     */
    if (sqTools::postVars('t','name','gender') && sqSession::vaildToken($_POST['t'], 'token', false)) {

      sqTools::jStatus($this->DB()->PExecute('UPDATE sq_users set name=?, sex=? WHERE uid=?', trim($_POST['name']), $_POST['gender'], sqSession::Get('uid')));

    } else {
      sqTools::jStatus();
    }
  }

}
