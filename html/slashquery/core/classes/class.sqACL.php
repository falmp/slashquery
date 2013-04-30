<?php
/**
 * sqACL - /slashquery/core/classes/class.sqACL.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqACL extends sqBase {

  public function __construct(sqRouter $sqRouter, Closure $DB) {
    $this->router = $sqRouter;
    $this->DB = $DB;
    $this->usertype = 1;

    if (!sqSession::Get('uid') && isset($_COOKIE['rmb'])) {
      $this->rememberMe();
    }
  }

  protected function rememberMe() {
    /**
     * Remember me option
     */

    if ($cdata = sqCookies::Get('rmb')) {
      list($lid, $hash, $token) = $cdata;
      $now = gmdate("Y-m-d H:i:s");
      $rs = $this->DB()->PGetRow('SELECT uid, email FROM sq_users WHERE uid=(select uid from sq_users_logs WHERE id=?) AND HEX(cookie)=? AND ? < cookie_timeout;', $lid, $token, $now);
      if (!$rs) {
        sqTools::signOut();
      } elseif (sha1($_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_USER_AGENT']) == $hash) {
        sqSession::Start()->delSessionRef($rs['uid']);
        $GLOBALS[DALMP_SESSIONS_REF] = $rs['uid'];
        sqSession::Set('uid', $rs['uid']);
        sqSession::Set('email', $rs['email']);
      }
    }
  }

  protected function ACLs() {
    $uid = sqSession::Get('uid') ? sqSession::Get('uid') : 0;

    switch (true) {
      /**
       * rid 1 = anonymous users
       * rid 2 = authenticated users
       * rid 3 = admin users
       */
      case ($uid == 1):
        $ACL = json_decode($this->DB()->CacheGetOne(86400, 'SELECT ACL FROM sq_ACLs WHERE rid=3', 'group:ACL'), true);
        $this->usertype = 3;
        break;

      case $uid:
        if ($rids = $this->DB()->CachePGetCol(86400, 'SELECT rid FROM sq_users_roles WHERE uid=?', $uid, 'group:ACL')) {
          if (in_array(3, $rids)) { // check if user as admin permissions - rid = 3
            $ACL = json_decode($this->DB()->CacheGetOne(86400, 'SELECT ACL FROM sq_ACLs WHERE rid=3', 'group:ACL'), true);
            $this->usertype = 3;
          } else { // get user roles
            $rids[] = 2; // add role 2 for authenticated users
            $ACL = array();
            foreach ($rids as $rid) {
              $rs = json_decode($this->DB()->CachePGetOne(86400, 'SELECT ACL FROM sq_ACLs WHERE rid=? AND rid != 3', $rid, 'group:ACL'), true);
              if (is_array($rs)) {
                $ACL = sqTools::MergeArrays($ACL, $rs);
              }
            }
            $this->usertype = 2;
          }
        } else {
          $ACL = ($rs = $this->DB()->CacheGetOne(86400, 'SELECT ACL FROM sq_ACLs WHERE rid=2', 'group:ACL')) ? json_decode($rs, true) : null;
          $this->usertype = 2;
        }
        break;

      default : // anonymous user
        $ACL = ($rs = $this->DB()->CacheGetOne(86400, 'SELECT ACL FROM sq_ACLs WHERE rid=1', 'group:ACL')) ? json_decode($rs, true) : null;
        $this->usertype = 1;
    }

    /**
     * depure ACL
     */
    switch (true) {
      case $this->router->isCpanel:
        $ACL = isset($ACL['cpanel']) ? $ACL['cpanel'] : array();
        ksort($ACL);
        break;

      default :
        $ACL = isset($ACL['site']) ? $ACL['site'] : array();
        ksort($ACL);
        break;
    }

    $this->ACL = $ACL;

    sqLogger::log(__CLASS__, "usertype: $this->usertype - command: " . $this->router->command . ' - ACL:', $this->ACL);
  }

  /**
   * check the ACL
   * input must be one ore multiple perms example: check(1); or check(1,2,3);
   */
  public function check() {
    $args = func_get_args();

    /**
     * get the ACLs
     */
    $this->ACLs();

    /**
     * full access to admin
     */
    if (sqSession::Get('uid') == 1 || $this->usertype == 3) {
      return true;
    }

    /**
     * show the cpanel login page to everybody
     */
    if (!sqSession::Get('uid') && $this->router->isCpanel) {
      return true;
    }

    /**
     * allow registered users to change their details and password on the cpanel
     */
    if (sqSession::Get('uid') && $this->router->isCpanel && $this->router->module == 'cpanel') {
      return true;
    }

    /**
     * check perms based on args
     */
    if (current($args)) {
      $i = 1;
      foreach ($args as $permission) {
        (array_key_exists($this->router->module, $this->ACL) && array_key_exists($permission, $this->ACL[$this->router->module])) ? true : $i++;
      }
      return ($i > 1) ? false : true;
    } else {
      return array_key_exists($this->router->module, $this->ACL) ? true : false;
    }
  }

}
