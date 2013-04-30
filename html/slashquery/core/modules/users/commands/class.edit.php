<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.edit.php
 *
 * edit user
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqEdit extends sqFinalCommand {

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
      /**
       * declare scripts to be used when editing the user
       */
      $tpl['js'] = array('vendor/gibberish-aes.min.js', 'vendor/jquery.blockUI.js','vendor/jquery.sha1.js','vendor/jquery.validate.min.js', 'userEdit.js');

      /**
       * edit ID is the argument of the URL
       * /cpanel/users/edit/1
       */
      $eid = $this->router->arguments[0];

      $tpl['user'] = $this->DB()->FetchMode('ASSOC')->PGetRow('SELECT uid, name, email, sex, status FROM sq_users WHERE uid=?', $eid);

      /**
       * by default user uid = 1 is admin
       */
      if ($tpl['user'] > 1) {
        /**
         * get available roles for usertype > 2
         */
        $tpl['roles'] = (new coreACL($this->router, $this->DB))->getRoles(2);
        $tpl['uroles'] = $this->DB()->FetchMode('ASSOC')->PGetASSOC('SELECT rid, uid FROM sq_users_roles WHERE uid=?', $eid);
      }

      $tpl['token'] = sqSession::token();

      $this->notify();
    }

  }

}
