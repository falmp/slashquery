<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.editUser.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqEditUser extends sqFinalRule {

  protected function evaluate() {

    /**
     * check for token and uid keep tooken
     */
    if (sqTools::postVars('token','uid') && sqSession::validToken($_POST['token'], 'token', false)) {
      sqTools::jStatus( (new coreUser($this->DB))->edituser($_POST) );
    } else {
      sqTools::jStatus();
    }

  }

}
