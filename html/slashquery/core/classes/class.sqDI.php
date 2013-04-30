<?php
/**
 * sqDI - /slashquery/core/classes/class.sqDI.php
 * Dependecy Injector
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

abstract class sqDI {
  /**
   * Container for the classes
   * @var array
   */
  protected $c = array();

  /**
   * Share the classes
   *
   * @param Closure $callable A closure to wrap for uniqueness
   * @return Closure The wrapped closure
   */
  protected function share(Closure $callable) {
    return function ($c = null) use ($callable) {
      static $object = null;
      if (is_null($object)) {
        $object = $callable($c);
      }
      return $object;
    };
  }

  /**
   * Dispatch the classes
   *
   * @param string $name
   * @param string $args
   * @return chainable object
   */
  public function __call($name, $args) {
    if ($this->c[$name] instanceof Closure) {
      $c = $this->c[$name];
      return call_user_func_array($c, $args);
    } else {
      throw new Exception("SlashQuery class ({$name}) does not exist", 0);
    }
  }

  /**
   * return the Closures
   *
   * @param string Closure name
   * @return closure
   */
  public function __get($key) {
    if (array_key_exists($key, $this->c)) {
      return $this->c[$key];
    }
  }

}
