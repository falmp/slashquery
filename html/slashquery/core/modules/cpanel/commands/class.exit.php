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
    /**
     * remove user from the abuse DB so that on his next login he don't see the
     * reCAPTCHA
     */
    sqAbuse::Del(sqSession::Get('email'));

    sqTools::signOut();
  }

}
