<?php
/**
 * cPanel - /slashquery/core/modules/ACL/commands/class.modules.php
 *
 * list modules
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqModules extends sqFinalCommand {

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
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'modules.js');
      $tpl['command_tpl'] = 'modules.tpl';
      $modules = new coreACL($this->router, $this->DB);
      $tpl['site_modules'] = $modules->getSiteModules();
      $tpl['cpanel_modules'] = $modules->getCpanelModules();
      $this->notify();
    }

  }

}
