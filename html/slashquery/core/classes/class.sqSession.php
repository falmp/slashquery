<?php
/**
 * sqSessions - /slashquery/core/classes/class.sqSessions.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSession {

  /**
   * session start
   *
   * @return DALMP_Sessions instance
   */
  public static function Start() {
    static $inst = null;
    if (is_null($inst)) {
      switch (explode(':',DALMP_SESSIONS_CACHE_TYPE)[0]) {
        case 'mysql':
          $storage = new DALMP (DSN);
          break;

        case 'redis':
        case 'memcache':
          list($type, $host, $port, $compress) = @explode(':', DALMP_SESSIONS_CACHE_TYPE) + array(null, null, null, null);
          $storage = (new DALMP_Cache($type))->host($host)->port($port)->compress($compress);
          break;

        default :
          $storage = 'sqlite';
      }
      $inst = new DALMP_Sessions($storage);
    }
    return $inst;
  }

  public static function Set($name, $value) {
    $_SESSION[$name] = $value;
  }

  public static function Get($name) {
    return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
  }

  public static function Remove($name) {
    $_SESSION[$name] = null;
    unset($_SESSION[$name]);
  }

  public static function destroy() {
    $_SESSION = array();
    return session_destroy();
  }

  /**
   * token
   *
   * creates a token and  store it  on the sessions to help prevent CSRF
   *
   * @param string $name
   * @param sting $algo
   * @return token
   */
  public static function token($name='token', $algo=null) {
    self::Start();
    return (self::Get($name) ?: self::Set($name, self::salt($algo))) ?: self::Get($name);
  }

  /**
   * validToken
   *
   * @param string $token
   * @param string $name
   * @param boolean $clear
   * @return boolean
   */
  public static function validToken($token, $name='token', $clear=true) {
    if (self::token($name) == $token) {
      if ($clear) {
        self::Remove($name);
      }
      return true;
    } else {
      return false;
    }
  }

  /**
   * clearToken
   *
   * @param string $name
   */
  public static function clearToken($name='token') {
    self::Remove($name);
  }

  /**
   * salt - creates a salt (default sha1)
   *
   * @param $algo
   * @return $algo;
   */
  public static function salt($algo=null) {
    $algo = $algo ?: 'sha1';
    return hash($algo, openssl_random_pseudo_bytes(32));
  }

}
