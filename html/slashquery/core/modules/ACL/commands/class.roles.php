<?php
/**
 * cPanel - /slashquery/core/modules/ACL/commands/class.roles.php
 *
 * set Roles
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqRoles extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
      $tpl['js'] = array('vendor/jquery.blockUI.js','vendor/jquery.validate.min.js', 'roles.js');

      $tpl['command_tpl'] = 'roles.tpl';

      $tpl['roles'] = $this->DB()->FetchMode('ASSOC')->getAll('SELECT * FROM sq_roles ORDER BY FIELD(rid,1,2,3) DESC, rid');

			$tpl['token'] = sqSession::token();

      $this->notify();
    }

  }

}
