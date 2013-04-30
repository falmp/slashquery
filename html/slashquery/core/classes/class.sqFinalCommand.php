<?php
/**
 * sqFinalCommand - /slashquery/core/classes/class.sqFinalCommand.php
 * Final abstract class for commands
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

abstract class sqFinalCommand extends sqBase {

  final public function __construct(sqSite $SQ, sqDispatcher $dispatcher) {
    $this->SQ = $SQ;

    /**
     * @var sqRouter
     */
    $this->router = $SQ->router;

    /**
     * @var sqTPL
     */
    $this->TPL = $SQ->TPL;

    /**
     * @var DALMP
     */
    $this->DB = $SQ->DB;

    /**
     * @var sqACL
     */
    $this->ACL = $SQ->ACL;

    /**
     * @var sqDispatcher
     */
    $this->dispatcher = $dispatcher;

    /**
     * evaluate the rule
     */
    $this->run();
  }

  abstract public function run();

  /**
   * observer pattern
   * notify the viewers
   */
  public function notify() {
    $this->dispatcher->notify();
  }

  /**
   * observer pattern
   * attach viwers
   */
  public function attach(SplObserver $observer) {
    $this->dispatcher->attach($observer);
  }

}
