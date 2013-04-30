<?php
/**
 * default - /slashquery/sites/default/modules/main/commands/class.default.php
 *
 * default class used when there is no Command
 *
 * @package SlashQuery
 */

class sqDefault extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

    $this->notify();
  }

}
