<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.user.php
 *
 * get user details
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqUser extends sqFinalCommand {

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
			$tpl['ulog'] = sqSession::Get('ulog');
	    $tpl['module_tpl'] = 'user.tpl';

			$tpl['js'] = array('vendor/gibberish-aes.min.js', 'vendor/jquery.blockUI.js', 'vendor/jquery.sha1.js', 'vendor/jquery.validate.min.js', 'userDetails.js');

			$tpl['token'] = sqSession::token();

			$tpl['uDetails'] = $this->DB()->FetchMode('ASSOC')->PgetRow('SELECT name, sex FROM sq_users where uid=?', $uid);

			$this->notify();
		} else {
			sqTools::signOut();
		}
	}

}
