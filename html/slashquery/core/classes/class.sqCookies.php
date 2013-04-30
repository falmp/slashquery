<?php
/**
 * sqCookies - /slashquery/core/classes/class.sqCookies.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCookies {

  /**
   * set - create a cookie, where data is an array
   *
   * @param string $name
   * @param array $data
   * @param int $expire
   * @param string $path
   * @param string $domain
   * @param bool $secure
   * @param bool $httponly
   * @return boolean
   */
  public static function Set($name = 'sq', array $data, $expire = 0, $path = '/', $domain = false, $secure = false, $httponly = true) {

    /**
     * cookie will containg a leading dot (wildcard)
     */
    if ($domain === false) {
      $domain = $_SERVER['HTTP_HOST'];
    }

    $cookie = sqAES::crypt( SITE_SALT, json_encode( $data )  );

    $expire = time() + $expire;

    return setcookie($name, $cookie, $expire, $path, $domain, $secure, $httponly);
  }

  /**
   * get
   *
   * @param string $cookie
   * @return boolean or cookie
   */
  public static function Get($cookie) {
    if (isset($_COOKIE[$cookie])) {
      return @json_decode( sqAES::decrypt(SITE_SALT, $_COOKIE[$cookie]), 1 );
    } else {
      return false;
    }
  }

  /**
   * Delete
   *
   * @param string $name
   * @param string $path
   * @param string $domain
   * @return boolean
   */
  public static function Delete($name = 'sq', $path = '/', $domain = false) {
    if ($domain === false) {
      $domain = $_SERVER['HTTP_HOST'];
    }

    $expire = time() - 3600;

    return setcookie($name, array(), $expire, $path, $domain);
  }

}
