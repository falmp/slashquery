<?php
/**
 * sqFinalRule - /slashquery/core/classes/class.sqFinalRule.php
 * Final abstract class for sqRules
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

abstract class sqFinalRule extends sqBase {

  final public function __construct(sqSite $SQ) {
    $this->SQ = $SQ;

    /**
     * @var sqRouter
     */
    $this->router = $SQ->router;

    /**
     * @var DALMP
     */
    $this->DB = $SQ->DB;
    /**
     * @var sqACL
     */
    $this->ACL = $SQ->ACL;

    /**
     * evaluate the rule
     */
    $this->evaluate();
  }

  abstract protected function evaluate();

}
