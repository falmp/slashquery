<?php
/**
 * sqAbuse - /slashquery/core/classes/class.sqAbuse.php
 *
 * keep record of 'possible' IP/users that have been used against
 * Brute force attacks on a SQLite3 DB /slashquery/abuse.db
 *
 * Later maybe this will be changed to use memcache/redis, for now the idea of
 * having a not distribuited database is on a cloud enviroment only certain
 * instance (VPS) can fire the reCAPTCHA instead of affecting the full site
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAbuse {

  /**
   * match - search the db for the the IP of the request and if passed also for
   * the user
   *
   * @param string $user
   * @return boolean
   */
  public static function match($email = null) {
    try {
      $file_db = SQ_ROOT . 'slashquery/abuse.db';
      $db = new SQLite3($file_db);
      return @$db->querySingle("SELECT id FROM abuse WHERE IP = '". sqTools::getIPv4() ."' OR email = '$email'");
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * Add login information (IP, User Agent, email) to the abuse database
   *
   * @param string $email
   * @return boolean
   */
  public static function Add($email = null) {
    if (isset($email) && !sqTools::validEmail($email)) {
      return false;
    }
    $file_db = SQ_ROOT . 'slashquery/abuse.db';
    $db = new SQLite3($file_db);
    $db->busyTimeout(2000);
    $db->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $db->exec('CREATE TABLE IF NOT EXISTS abuse (id INTEGER PRIMARY KEY, IP VARCHAR (15) NOT NULL, email VARCHAR(128), UA TEXT, cdate DATE)');
    $sql = "INSERT OR REPLACE INTO abuse VALUES(NULL, '" . sqTools::getIPv4() . "', '$email', '" . base64_encode($_SERVER['HTTP_USER_AGENT']) . "', '" . gmdate('Y-m-d H:i:s'). "')";
    if (!$db->exec($sql)) {
      return false;
    }
    $db->busyTimeout(0);
    $db->close();
    return true;
  }

  /**
   * Del - delete record from database
   *
   * @param string $email
   * @return boolean
   */
  public static function Del($email= null) {
    if (isset($email) && !sqTools::validEmail($email)) {
      return false;
    }
    $file_db = SQ_ROOT . 'slashquery/abuse.db';
    $db = new SQLite3($file_db);
    return @$db->query("DELETE FROM abuse WHERE IP = '". sqTools::getIPv4() ."' OR email='$email'");
  }

  /**
   * DelID - delete record from database
   *
   * @param string $email
   * @return boolean
   */
  public static function DelID($id) {
    $file_db = SQ_ROOT . 'slashquery/abuse.db';
    $db = new SQLite3($file_db);
    $id = (int) $id;
    return @$db->query("DELETE FROM abuse WHERE id=$id");
  }

  /**
   * Flush - deletes database
   */
  public static function Flush() {
    return unlink(realpath(SQ_ROOT . 'slashquery/abuse.db'));
  }

  /**
   * GetAll
   *
   * @return SQLite3Result
   */
  public static function GetAll() {
    try {
      $file_db = SQ_ROOT . 'slashquery/abuse.db';
      $db = new SQLite3($file_db);
      return @$db->query('SELECT * FROM abuse');
    } catch (Exception $e) {
      return false;
    }
  }

}
