<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.delUser.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDelUser extends sqFinalRule {

  protected function evaluate() {
    /**
     * check for uid and token
     */
    if (sqTools::postVars('uid','token') && sqSession::vaildToken($_POST['token'], 'token', false) ) {
      sqSession::Start()->delSessionRef($_POST['uid']);
      sqTools::jStatus((new coreUser($this->DB))->delUser($_POST['uid']));
    } else {
      sqTools::jStatus();
    }

  }

}
