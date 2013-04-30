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

class sqUOpenID extends sqFinalCommand {

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
	    $tpl['module_tpl'] = 'openid.tpl';

			$tpl['js'] = array('vendor/jquery.blockUI.js','vendor/jquery.validate.min.js', 'uopenid.js');

			$tpl['token'] = sqSession::token();

			if (sqSession::Get('error')) {
				sqSession::Remove('error');
				$tpl['error'] = true;
			}

			$tpl['uOpenIDs'] = $this->DB()->FetchMode('ASSOC')->PgetAll('SELECT id, openid, cdate FROM sq_users_openids where uid=?', $uid);

			$this->notify();
		} else {
			sqTools::signOut();
		}
	}

}
