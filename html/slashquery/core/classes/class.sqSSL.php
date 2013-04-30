<?php
/**
 * sqSSL - /slashquery/core/classes/class.sqSSL.php
 * wrapper to openssl
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSSL {
  private static $Cipher = '-aes-256-cbc';

  /**
   * decrypt
   *
   * @param string $passphrase
   * @param data $edata
   * @param string $Cipher
   * @return dencrypted data
   */
  public static function decrypt($passphrase, $edata, $Cipher = null) {
    $Cipher = $Cipher ?: self::$Cipher;

    $command = "openssl enc -d $Cipher -a -k $passphrase";

    $descriptors = array(
      0 => array('pipe', 'r'),
      1 => array('pipe', 'w'),
      2 => array('file', SQ_ROOT . '../logs/error-sqSSL.txt', 'a'));

    $process = proc_open($command, $descriptors, $pipes);

    if (is_resource($process)) {
      fwrite($pipes[0], "$edata\n");
      fclose($pipes[0]);
      $data = stream_get_contents($pipes[1]);
      fclose($pipes[1]);
      proc_close($process);
      return $data;
    }
  }

  /**
   * crypt
   *
   * @param string $passphrase
   * @param data $edata
   * @param string $Cipher
   * @return dencrypted data
   */
  public static function crypt($passphrase, $data, $Cipher = null) {
    $Cipher = $Cipher ?: self::$Cipher;

    $command = "openssl enc -e $Cipher -a -k $passphrase";

    $descriptors = array(
      0 => array('pipe', 'r'),
      1 => array('pipe', 'w'),
      2 => array('file', SQ_ROOT . '../logs/error-sqSSL.txt', 'a'));

    $process = proc_open($command, $descriptors, $pipes);

    if (is_resource($process)) {
      fwrite($pipes[0], "$data\n");
      fclose($pipes[0]);
      $data = stream_get_contents($pipes[1]);
      fclose($pipes[1]);
      proc_close($process);
      return trim($data);
    }
  }

}
