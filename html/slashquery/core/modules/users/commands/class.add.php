<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.add.php
 *
 * add an user
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAdd extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

    /**
     * if user already logged in show admin pannel
     */
		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
      $tpl['js'] = array('vendor/gibberish-aes.min.js', 'vendor/jquery.blockUI.js', 'vendor/jquery.sha1.js', 'vendor/jquery.validate.min.js', 'userAdd.js');

      /**
       * get list of excluding the default
       * 1 - anonymous users
       * 2 - authenticaed users
       */
      $tpl['roles'] = (new coreACL($this->router, $this->DB))->getRoles(2);

      $tpl['token'] = sqSession::token();

      $this->notify();
    }

  }

}
