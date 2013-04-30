<?php
/**
 * upload cPanel extension - /slashquery/sites/default/cpanel/upload/commands/class.default.php
 *
 * default class used when there is no command
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDefault extends sqFinalCommand {

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
			$tpl['token'] = sqSession::token();
	    $this->notify();
		} else {
			sqTools::signOut();
		}

  }

}
