<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * sqAutoloader - /slashquery/core/classes/class.autoload.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqAutoLoader {
  private static $paths = array();

  /**
   * add a Path to the autoloader
   */
  public static function addPath($path) {
    $path = realpath($path);
    if ($path) {
      self::$paths[] = $path;
    }
  }

  /**
   * get the current paths
   */
  public static function getPaths() {
    return self::$paths;
  }

  /**
   * autoload the functions
   */
  public static function autoload($class) {
    switch ($class) {
      case (preg_match('/^dalmp/i', $class) ? $class : !$class):
        $class_file = DALMP;
        break;

      case (preg_match('/^core|sq/i', $class) ? $class : !$class):
        $class_file = SQ_ROOT . "slashquery/core/classes/class.$class.php";
        break;

      default:
        #$trace = debug_backtrace();
        #$dir = dirname($trace[1]['file']);
        $class_file = null;
    }

    if (is_readable($class_file)) {
      require_once $class_file;
      return true;
    } else {
      $class_file = sprintf('class.%s.php', $class);
      /**
       * case insensitive search
       */
      foreach (self::$paths as $path) {
        if ( is_readable($path . DIRECTORY_SEPARATOR . strtolower($class_file) )) {
          require_once $path . DIRECTORY_SEPARATOR . strtolower($class_file);
          return true;
        } elseif ( is_readable($path . DIRECTORY_SEPARATOR . $class_file )) {
          require_once $path . DIRECTORY_SEPARATOR . $class_file;
          return true;
        }
      }

    }

    return false;
  }

}

spl_autoload_register(array('sqAutoLoader', 'autoload'));
