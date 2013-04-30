<?php
/**
 * upload cPanel extension - /slashquery/sites/default/cpanel/upload/commands/class.create.php
 *
 * default class used when there is no command
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCreate extends sqFinalCommand {

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

			$tpl['css'] = array('bootstrap-fileupload.min.css' => 'ext');
			$tpl['js'] = array('vendor/bootstrap-fileupload.min.js' => 'ext',
												 'upload.js' => 'ext',
												 'vendor/jquery.blockUI.js',
												 'vendor/jquery.validate.min.js');

			$tpl['token'] = sqSession::token();
			$tpl['uuid'] = $this->DB()->UUID();

	    $this->notify();
		} else {
			sqTools::signOut();
		}

  }

}
