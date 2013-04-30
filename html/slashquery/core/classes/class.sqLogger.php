<?php
/**
 * sqLogger - /slashquery/core/classes/class.sqLogger.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqLogger {
  private static $log;
  private static $nolog;
  private static $counter = 1;
  private static $debug = false;
  private static $mplt;

  public static function init($time, $start=false) {
    if ($start) {
      self::$mplt = new sqMPLT(4, $time);
      self::$debug = true;
      if (!file_exists(DEBUG_FILE)) {
        @mkdir(dirname(DEBUG_FILE),0700,true);
      }
      $dash = str_repeat('-', 80) . PHP_EOL;
      $log = 'START ' . @date('c') . " - [$_SERVER[HTTP_HOST] - $_SERVER[REMOTE_ADDR]]".PHP_EOL.$dash;
      file_put_contents(DEBUG_FILE,$log,FILE_APPEND);
    } else {
     self::$nolog = true;
    }
  }

  public static function log() {
    if (self::$nolog) {
      return;
    }
    $args = func_get_args();
    if (end($args) == 1) {
      array_pop($args);
      $mail = new sqMail();
      $mail->Send(REPORT_ERROR_MAIL_SUBJECT, array(REPORT_ERROR_MAIL_FROM => REPORT_ERROR_MAIL_FROMNAME), array(REPORT_ERROR_MAIL_TO), json_encode($args));
    }
    $out = json_encode($args);
    $etime = number_format(microtime(true) - self::$mplt->getStime(), 3);
    $out = self::$counter." - $etime - ".stripslashes($out). PHP_EOL;
    file_put_contents(DEBUG_FILE,$out,FILE_APPEND);
    self::$counter++;
  }

  public static function End() {
    if (self::$nolog) {
      return;
    }
    $dash = str_repeat('-', 80) . PHP_EOL;
    $log = $dash.'END   ' . @date('c') . ' - [Time: '. self::$mplt->getPageLoadTime().' - Memory: '.self::$mplt->getMemoryUsage(1).']'.PHP_EOL.$dash;
    file_put_contents(DEBUG_FILE,$log,FILE_APPEND);
  }

}
