<?php
/**
 * coreUser - /slashquery/core/classes/class.coreUsers.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class coreUser extends sqBase {

	public function __construct(Closure $DB) {
		$this->DB = $DB;
	}

	/**
   * query config values
   *
   * @param string $value
   * @return configuration value
   */
  public function sqConfig($value) {
    return $this->DB()->CachePgetOne('SELECT config_value FROM sq_config WHERE config_name=?', $value);
  }

	public function checkExists($post) {
		if ($post == 'password') {
			$rs = $this->DB()->GetRow("SELECT * FROM core_users WHERE email='" . $this->getEmail() . "'");
			if (!$rs) {
				$this->setMsg($this->DB()->ErrorMsg());
				return false;
			} else {
				return true;
			}
		} else {
			$value = ($post == 'login') ? $this->getLogin() : $this->getEmail();
			$rs = $this->DB()->getRow("SELECT * FROM core_users WHERE $post=" . quote_smart($value) . "");
			if (!$rs) {
				$this->setMsg($this->DB()->ErrorMsg());
				return true;
			} else {
				return false;
			}
		}
	}

  /**
	 * addUser - creates a user
	 *
	 * @param string $name
	 * @param encrypted $email
	 * @param encrypted/array $password if openid array used array('openid' => $oi)
	 * @param array $roles
	 * @param int $gender
	 * @param int $status
	 * @param array $captcha = time - captcha
	 *
	 * @return boolean
	 */
	public function addUser($name = '', $email, $password, $roles = array(), $gender = 0, $status = 0, array $captcha = array()) {
		$email = strtolower(trim($email));
		if (is_array($password)) {
			$openid = true;
			$password = current($password);
		} else {
			$password = sqTools::hasher(hash('sha256', $email . $password));
		}

		if (!sqTools::validEmail($email)) {
		  return false;
		}

		if (!empty($roles)) {
			$values ='';
			foreach ($roles as $rid) {
				$values .= "(LAST_INSERT_ID(), $rid),";
			}
			$values = rtrim($values,',');
			$sql = "INSERT INTO sq_users_roles (uid,rid) VALUES $values";
		  $this->DB()->StartTrans();
			$this->DB()->PExecute("INSERT INTO sq_users SET name=?, email=?, sex=?, password=?, status=?, cdate=NOW(), uuid=UNHEX(REPLACE(UUID(), '-', ''))", $name, $email, $gender, $password, $status);
			$this->DB()->Execute($sql);
			return $this->DB()->CompleteTrans();
		} else {

			$sql = "INSERT INTO sq_users SET name=?, email=?, sex=?, password=?, status=?, cdate=NOW(), uuid=UNHEX(REPLACE(UUID(), '-', '')), captcha=UNHEX(?)";

			/**
			 * db only store the 32 chars in binary form
			 * 24 are random bytes 8 are the time() in hex
			 */
			$captcha = empty($captcha) ? 0 : end($captcha);

			if (isset($openid)) {
        $this->DB()->StartTrans();
				$this->DB()->PExecute($sql, $name, $email, $gender, 'oi', $status, $captcha);
				$oi = rtrim(preg_replace('#^https?://#', '', $password), '/');
				if (!$this->DB()->PExecute('INSERT INTO sq_users_openids (uid,openid) VALUES(LAST_INSERT_ID(),?)', $oi)) {
          $this->DB()->RollBackTrans();
          return false;
        }
        return $this->DB()->CompleteTrans();
			} else {
				return $this->DB()->PExecute($sql, $name, $email, $gender, $password, $status, $captcha);
			}
		}
	}

  /**
	 * edituser
	 *
	 * @param array $data keys or array are: uid, name, password, gender. status,
	 * roles, oi
	 *
	 * @return boolean
	 */
	public function edituser($data) {
		if (!sqTools::is_number($data['uid'])) {
			return false;
		}

		$status = isset($data['status']) ? $data['status'] : 1;

		/**
		 * kill live user if status = 0
		 */
		if (!$status) { // destroy session of a users with status =0
			sqSession::Start()->delSessionRef($data['uid']);
		}

	  /* start DB transaction */
		$this->DB()->StartTrans();

		if ( isset($data['roles']) && !empty($data['roles']) ) {
			/* delete all user roles */
			$this->DB()->PExecute('DELETE FROM sq_users_roles WHERE uid=?', $data['uid']);
			/* set new roles */
			$values ='';
			foreach ($data['roles'] as $rid) {
				$values .= "($data[uid], $rid),";
			}
			$values = rtrim($values,',');
			$sql = "INSERT INTO sq_users_roles (uid,rid) VALUES $values";
			$this->DB()->Execute($sql);
		} else {
      $this->DB()->PExecute('DELETE FROM sq_users_roles WHERE uid=?', $data['uid']);
    }

		if (empty($data['password'])) {
			$this->DB()->PExecute('UPDATE sq_users SET name=?, sex=?, status=? WHERE uid=?', $data['name'], $data['gender'], $status, $data['uid']);
		} else {
			/* decrypt password and create a hash*/
			$email = $this->DB()->PGetOne('SELECT email FROM sq_users WHERE uid=?', $data['uid']);
			$password = sqTools::hasher( hash('sha256', $email . sqAES::decrypt( sqSession::token(), $data['password'] ) ) );
			$this->DB()->PExecute('UPDATE sq_users SET name=?, sex=?, password=?, status=? WHERE uid=?', $data['name'], $data['gender'], $password, $status, $data['uid']);
		}

		return $this->DB()->CompleteTrans();
	}

	/**
	 * delete User
	 *
	 * @param int $uid

	 * @return boolean
	 */
	public function delUser($uid) {
		return isset($uid) ? $this->DB()->PExecute('DELETE FROM sq_users WHERE uid != 1 AND uid=?',$uid) : false;
	}

	/**
	 * resetCaptcha - send the email only after $tdiff as passed
	 *
	 * @param string $email
	 * @param array $captcha
	 * @param int $tdiff seconds after mdate (default 7 seconds 420 seconds)
	 *
	 * @return string based on status saved on DB, RP = 2, NU = 0 or false
	 */
	public function resetCaptcha($email, array $captcha, $tdiff = 420) {
		$captcha = end($captcha);

		if ($rs = $this->DB()->FetchMode('ASSOC')->PGetRow('SELECT uid, status, TIME_TO_SEC(timediff(NOW(), mdate)) AS tdiff FROM sq_users WHERE email=?', $email)) {
			switch (true) {
				/**
				 * user status 1 - reset password
				 */
				case $rs['status']:
					if ($rs['status'] < 2 || $rs['tdiff'] > $tdiff) {
						return $this->DB()->PExecute('UPDATE sq_users SET status=2, mdate=NOW(), captcha=UNHEX(?) WHERE uid=?', $captcha, $rs['uid']) ? 'RP' : false;
					}
					break;

				/**
				 * user status 0 - resend confirmation code
				 */
				default :
					if ($rs['tdiff'] > $tdiff) {
						return $this->DB()->PExecute('UPDATE sq_users SET status=0, mdate=NOW(), captcha=UNHEX(?) WHERE uid=?', $captcha, $rs['uid']) ? 'NU' : false;
					} else {
						return false;
					}
			}
		} else {
			return false;
		}
	}

} // end of class
