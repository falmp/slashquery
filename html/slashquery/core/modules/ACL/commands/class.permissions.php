<?php
/**
 * cPanel - /slashquery/core/modules/ACL/commands/class.permissions.php
 *
 * set permissions
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqPermissions extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'permissions.js');
      /**
       * @var coreACL
       */
      $ACL = new coreACL($this->router, $this->DB);

      /**
       * after submiting the form check if the url contains the arguments
       * (default or roles) if it does store the new values
       */
      if (isset($this->router->arguments[0])) {
        if (in_array($this->router->arguments[0], array('default','roles'))) {
          $ACL->updateRoles();
        }
      }

      /**
       * get current modules and ACL's from DB
       */
    	$tpl['modules'] = $ACL->modules;
      $tpl['ACLs'] = $ACL->getACLs();
      if ($ACL->getRoles()) {
      	$roles = array(0 => 'Default') + $ACL->getRoles();
      } else {
      	$roles = 0;
      }
      $tpl['roles'] = $roles;
      /**
       * roleID is set after submiting the form, the constructor search for
       * $_POST['rid'] and set it
       */
      $tpl['roleID'] = $ACL->roleID;

      $tpl['command_tpl'] = $ACL->roleID ? 'permissions.tpl' : 'permissions-default.tpl';

      $tpl['token'] = sqSession::token();

      $this->notify();
    }

  }

}
