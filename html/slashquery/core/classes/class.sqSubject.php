<?php
/**
 * sqSubject - /slashquery/core/classes/class.sqSubject.php
 * observer pattern
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSubject extends sqBase implements SplSubject {
  private $observers;

  public function __construct() {
    $this->observers = new SplObjectStorage();
  }

  public function attach(SplObserver $observer) {
    $this->observers->attach($observer);
  }

  public function detach(SplObserver $observer) {
    $this->observer->detach($observer);
  }

  public function notify() {
    foreach ($this->observers as $observer) {
      $observer->update($this);
    }
  }
}
