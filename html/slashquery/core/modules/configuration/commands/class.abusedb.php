<?php
/**
 * cPanel - /slashquery/core/modules/configuration/commands/class.abuse.php
 *
 * Abuse module for managing the abuse DB SQLite 3
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAbuseDB extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
			$tpl['command_tpl'] = 'abusedb.tpl';
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'cabuse.js');

			$tpl['abuseDB'] = sqAbuse::GetAll();

			$this->notify();
    }

  }

}
