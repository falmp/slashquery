<?php
/**
 * sqBase - /slashquery/core/classes/class.sqBase.php
 * base class containing magic methods
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

abstract class sqBase {
  private $data = array();

  public function __set($key, $value) {
    if (method_exists($this, $method = $key)) {
      $this->$method($value);
    } else {
      if (is_string($value)) {
        $value = trim($value);
        $value = !strcmp(intval($value), $value) ? (int) $value : (!strcmp(floatval($value), $value) ? (float) $value : $value);
      }
      $this->data[$key] = $value;
    }
  }

  public function __get($key) {
    if (array_key_exists($key, $this->data)) {
      return $this->data[$key];
    }
  }

  public function __isset($key) {
    return isset($this->data[$key]);
  }

  public function __unset($key) {
    unset($this->data[$key]);
  }

  /**
   * helps to use $this->DB() when $this->DB is a closure
   *
   * @return eval closure
   */
  public function __call($name, $args) {
    if ($this->data[$name] instanceof Closure) {
      $c = $this->data[$name];
      return call_user_func_array($c, $args);
    } else {
      throw new Exception("method ({$name}) does not exist", 0);
    }
  }

}
