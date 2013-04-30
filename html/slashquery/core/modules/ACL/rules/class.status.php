<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/ACL/rules/class.status.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqStatus extends sqFinalRule {

  protected function evaluate() {

    /**
     * check for token, email, password and do not erase the token
     */
    if (sqTools::postVars('mid')) {
      sqTools::jStatus((new coreACL($this->router, $this->DB))->moduleStatus($_POST['mid']));
    } else {
      sqTools::jStatus();
    }

  }

}
