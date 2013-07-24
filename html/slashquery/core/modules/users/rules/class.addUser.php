<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.addUser.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAddUser extends sqFinalRule {

  protected function evaluate() {

    /**
     * check for token, email, password and do not erase the token
     */
    if (sqTools::postVars('t','e','p') && sqSession::validToken($_POST['t'], 'token', false)) {
      $email    = sqAES::decrypt( sqSession::token(), $_POST['e'] );
      $password = sqAES::decrypt( sqSession::token(), $_POST['p'] );
      $roles = isset($_POST['roles']) ? $_POST['roles'] : array();
      sqTools::jStatus((new coreUser($this->DB))->addUser($_POST['name'], $email, $password, $roles, $_POST['gender'], $_POST['status']));
    } else {
      sqTools::jStatus();
    }

  }

}
