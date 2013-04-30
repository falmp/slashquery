<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.exit.php
 *
 * exit and delete current sessions
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqExit extends sqFinalCommand {

  public function run() {
    sqTools::signOut();
  }

}
