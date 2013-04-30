<?php
/**
 * default - /slashquery/sites/default/modules/main/class.example.php
 *
 * example
 *
 * @package SlashQuery
 */

class sqExample extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('id')) {
      sqTools::jStatus($this->DB()->PExecute('DELETE FROM records WHERE id=? AND uid=?', $_POST['id'], sqSession::Get('uid')));
    } else {
      sqTools::jStatus();
    }

  }

}
