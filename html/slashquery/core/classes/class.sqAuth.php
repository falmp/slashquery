<?php
/**
 * sqAuth - /slashquery/core/classes/class.sqAuth.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAuth extends sqBase {

	public function __construct(Closure $DB) {
		$this->DB = $DB;
		sqSession::Start();
	}

  /**
   * query config values
   *
   * @param string $value
   * @return configuration value
   */
  private function sqConfig($value) {
    return $this->DB()->CachePgetOne('SELECT config_value FROM sq_config WHERE config_name=?', $value);
  }

  /**
	 * autenticate user
	 *
	 * @param boolean $ulog
	 * @return true or csrf, forcelogin, void_logging_twice
	 */
	public function login($ulog=false) {
    /**
		 * if $ulog set, "history" will be stored on session
		 */
		$this->ulog = $ulog;

		if (sqSession::Get('uid')) {
			return true;
		} else {
      /**
			 * clean email
			 */
			$this->email = strtolower(trim($this->email));

			/**
			 * get email so we can reuse init method with openID
			 */
			$rs = $this->DB()->FetchMode('ASSOC')->PGetRow('SELECT uid, email, password, login_count FROM sq_users WHERE email=? AND status !=0 LIMIT 1', $this->email);
      /* password = sha256 (email+sha1(password)) */
      $auth = $rs ? sqTools::hasher(hash('sha256', $this->email.$this->password), $rs['password']) : false;

			switch (true) {
				case $auth:
					if ($this->sqConfig('avoid_logging_twice')) {
						if (isset($this->forcelogin)) {
						  return $this->init($rs);
						} else {
							foreach (sqSession::Start()->getSessionsRefs() as $sid => $expiry) {
								if (key($expiry) == $rs['uid'] && current($expiry) > time()) {
									$uonline = true;
								}
							}
							/**
							 * if no forcelogin creates a loop going back to the avoid_loggin_twice
							 * if forcelogin goes to login
							 */
							return isset($uonline) ? ($this->sqConfig('allow_force_login') ? 'forcelogin' : 'avoid_logging_twice') : $this->init($rs);
						}
					} else {
						return $this->init($rs);
					}
					break;

				default :
					return false;
			}
		}
	}

  /**
	 * login using OpenID
	 *
	 * @param string $claimed_id
	 * @param boolean $ulog - save history information on session
	 * @return boolean
	 */
	public function loginOpenID($claimed_id, $ulog=false) {
		$oi = rtrim(preg_replace('#^https?://#', '', $claimed_id), '/');
		$this->ulog = $ulog;

		if ($rs = $this->DB()->FetchMode('ASSOC')->PGetRow('SELECT t1.uid, t1.email, t1.login_count FROM sq_users t1 JOIN sq_users_openids t2 USING(uid) WHERE t1.status !=0 AND t2.openid=? LIMIT 1', $oi)) {
			return $this->init($rs);
		} else {
			return false;
		}
	}

  /**
	 * init - creates a user session
	 *
	 * @access private
	 * @param array $user
	 */
	private function init($user) {
		if ($this->sqConfig('avoid_logging_twice')) {
			sqSession::Start()->delSessionRef($user['uid']);
		}

		/**
		 * save log
		 */
    $ip = sqTools::getIPv4();
		$lid = $this->DB()->PExecute('INSERT INTO sq_users_logs (uid, ip, host, ua, referer) VALUES (?,INET_ATON(?),?,?,?)', $user['uid'], $ip, gethostbyaddr($ip), $_SERVER['HTTP_USER_AGENT'], @$_SERVER['HTTP_REFERER']) ? $this->DB()->Insert_Id() : false;
		if (!$lid) {
			return false;
		}

		/**
		 * remember ME - rmb cookie
		 */
		if (isset($this->remember)) {
			$cookie_token = sqSession::salt('sha1');
			$rs = $this->DB()->PExecute('UPDATE sq_users SET login_count=login_count+1, cookie=UNHEX(?), cookie_timeout=DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE uid=?', $cookie_token, $user['uid']);
		  if (!$rs) {
				return false;
			}
			$cookie = array($lid,
											sha1($_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_USER_AGENT']),
											$cookie_token);
			// remember the user for 30 days
			sqCookies::Set('rmb', $cookie, 2592000);
		} else {
			$rs = $this->DB()->PExecute('UPDATE sq_users SET login_count=login_count+1, cookie=NULL WHERE uid=?', $user['uid']);
			if (!$rs) {
				return false;
			}
		}

		sqSession::Set('uid', $user['uid']);
		sqSession::Set('email', $user['email']);

		if ($this->ulog) {
			sqSession::Set('lcount', $user['login_count']);
			if ($ulog = $this->DB()->FetchMode('ASSOC')->PGetRow('SELECT cdate, INET_NTOA(ip) AS ip, host, ua FROM sq_users_logs WHERE uid=? ORDER BY id DESC LIMIT 1,1', $user['uid'])) {
	    	sqSession::Set('ulog', $ulog);
			}
		}

		/**
		 * save uid on DALMP ref
		 */
		$GLOBALS['UID'] = $user['uid'];

		/**
		 * regenerate session and creates fingerprint using 4 blocks of IP
		 * @see http://www.dalmp.com/sessions/regenerate_id
		 */
	  return sqSession::Start()->regenerate_id(4);
	}

} // end of class
