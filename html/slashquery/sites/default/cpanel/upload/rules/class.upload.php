<?php
/**
 * upload Cpanel extension - /slashquery/sites/default/cpanel/upload/rules/class.upload.php
 *
 * class to process the file upload
 *
 * @package SlashQuery
 */

class sqUpload extends sqFinalRule {

  protected function evaluate() {

    if (sqTools::postVars('token','name','tags') && sqSession::vaildToken($_POST['token'], 'token', false)) {
      sqTools::jStatus($_FILES);
    } else {
      sqTools::jStatus();
    }

  }

}
