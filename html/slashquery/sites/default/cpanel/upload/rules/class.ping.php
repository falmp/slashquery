<?php
/**
 * upload Cpanel extension - /slashquery/sites/default/cpanel/upload/rules/class.ping.php
 *
 * ping / pong to keep alive
 *
 * @package SlashQuery
 */

class sqPing extends sqFinalRule {

  protected function evaluate() {
    sqTools::jStatus('pong');
  }

}
