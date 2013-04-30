<?php
/**
 * sqFactory - /slashquery/core/classes/class.sqFactory.php
 * Factory class that initialize an object based on the command
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqFactory {
  static private $prefix = 'sq';

  /**
   * Manufacture - try to replace 'switch' with objects focus on a task
   *
   * @param string $path where to search for classes
   * @param sqSite $sqSite
   * @param sqDispatcher $dispatcher
   * @param sting $prefix
   *
   * @return object
   */
  public static function Manufacture($path, sqSite $SQ, sqDispatcher $dispatcher, $prefix = 'sq') {
    self::$prefix = $prefix;

    return ($SQ->router->command == 'sqRules') ? self::Rules($path, $SQ) : self::Commands($path, $SQ, $dispatcher);
  }

  /**
   * Rules factory - useful for ajax calls
   *
   * @param string $path where to search for classes
   * @param sqSite $SQ
   */
  public static function Rules($path, sqSite $SQ) {
    $args = $SQ->router->arguments;
    $rule = is_array($args) ? array_shift($args) : $args;
    $class_file = realpath($path.'/rules/'.sprintf('class.%s.php',$rule));

    if (is_readable($class_file)) {
      require_once $class_file;
      /**
       * class name by default uses prefix 'sq' this is for avoiding conflicts
       * when commands use reserved words like 'exit', 'default', etc;
       */
      $class = self::$prefix . ucfirst($rule);

      if (class_exists($class, false)) {
        return new $class($SQ);
      } else {
        throw new Exception("Class $class not found in $class_file");
        exit;
      }
    } else {
      throw new Exception("file: class.$rule.php is not readable");
      exit;
    }
  }

  /**
   * Commands factory
   *
   * @param string $path
   * @param sqSite $SQ
   * @param sqDispatcher $dispatcher
   */
  public static function Commands($path, sqSite $SQ, sqDispatcher $dispatcher) {
    $command = $SQ->router->command ?: 'default';
    $class_file = realpath($path.'/commands/'.sprintf('class.%s.php',$command));

    /**
     * class name by default uses prefix 'sq' this is for avoiding conflicts
     * when commands use reserved words like 'exit', 'default', etc;
     */
   $class = self::$prefix . ucfirst($command);

    /**
     * @var closure try to found the command class
     */
    $object = function() use (&$command, &$class_file, &$class, $SQ, $dispatcher) {

      if (is_readable($class_file)) {
        require_once $class_file;
        if (class_exists($class, false /** do not attempt autoload **/)) {
          return new $class($SQ, $dispatcher);
        } else {
          return false;
        }
      } else {
        throw new sqFactoryException("file: class.$command.php is not readable");
      }
    };

    /**
     * if the command is set but the class is not found,
     * fallback to use 'default' as default class
     */
    try {
      $object();
    } catch (sqFactoryException $e) {
      $command = 'default';
      $class_file = realpath($path.'/commands/class.default.php');
      $class = self::$prefix . ucfirst($command);
      $object();
    } catch (Exception $e) {
      throw $e;
    }
  }

}

/**
 * extend Exceptions for the factory class
 */
class sqFactoryException extends Exception {}
