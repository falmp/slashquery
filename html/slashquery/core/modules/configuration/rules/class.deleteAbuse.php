<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.deleteAbuse.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDeleteAbuse extends sqFinalRule {

  protected function evaluate() {

    sqTools::jStatus ( sqAbuse::Flush() );

  }

}
