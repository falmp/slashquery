<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.changePassword.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqChangePassword extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('t','p') && sqSession::vaildToken($_POST['t'], 'token', false)) {
      $password = sqAES::decrypt( sqSession::token(), $_POST['p'] );

      $uid = sqSession::Get('uid');

      /**
       * get email from DB
       */
      $email = $this->DB()->PGetOne('SELECT email FROM sq_users WHERE uid=?', $uid);

      /**
       * create password (hash) to store on DB
       */
      $password = sqTools::hasher(hash('sha256', $email . $password));

			sqTools::jStatus($this->DB()->PExecute('UPDATE sq_users SET password=? WHERE uid=?', $password, $uid));

    } else {
      sqTools::jStatus();
    }
  }

}
