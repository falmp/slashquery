<?php
/**
 * sqArray - /slashquery/core/classes/class.classArray.php
 * ArrayAccess
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 * @link http://www.php.net/manual/en/class.arrayaccess.php
 */

class sqArray implements ArrayAccess {
  private $container = array();

  public function __construct(array $data = array()) {
    foreach ($data as $key => $value) $this[$key] = $value;
  }

  public function offsetSet($offset, $value) {
		if (is_array($value)) $value = new self($value);
    if (is_null($offset)) {
      $this->container[] = $value;
    } else {
      $this->container[$offset] = $value;
    }
  }

	public function toArray() {
    $data = $this->container;
    foreach ($data as $key => $value) if ($value instanceof self) $data[$key] = $value->toArray();
    return $data;
  }

	public function offsetGet($offset) {
    return isset($this->container[$offset]) ? $this->container[$offset] : null;
  }

  public function offsetExists($offset) {
    return isset($this->container[$offset]);
  }

  public function offsetUnset($offset) {
    unset($this->container[$offset]);
  }

}
