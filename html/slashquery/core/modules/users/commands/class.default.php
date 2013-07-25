<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.default.php
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
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'users.js');
			$tpl['command_tpl'] = 'view.tpl';
			list($tusers, $active, $inactive) = $this->DB()->FetchMode('NUM')->GetRow('SELECT COUNT(uid), SUM(status > 0), SUM(status=0) FROM sq_users');
			$tpl['tusers'] = $tusers;
			$tpl['active'] = $active;
			$tpl['inactive'] = $inactive;

			$tpl['users'] = $this->DB()->FetchMode('ASSOC')->getAll("SELECT t1.uid, t1.name, t1.email, t1.cdate, t1.status, group_concat(t3.name ORDER BY t3.name ASC SEPARATOR '|') AS roles FROM sq_users t1 LEFT JOIN sq_users_roles t2 USING(uid) LEFT JOIN sq_roles t3 USING (rid) GROUP BY uid ORDER BY t1.uid DESC LIMIT 50");

      $tpl['token'] = sqSession::token();

      /**
       * rows per page = 50
       * @var int total pages
       */
      $tpl['Tpages'] = ceil($tusers / 50);

			$this->notify();
    }

  }

}
