<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * Reset Password - /slashquery/xx-10-32/RP.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

/**
 * if postvars token and np, change the password
 */
if (sqTools::postVars('token','np') && sqSession::vaildToken($_POST['token'], 'token', false)) {
  if ($user = $SQ->DB()->FetchMode('ASSOC')->PGetRow('SELECT uid, email FROM sq_users WHERE status=2 AND HEX(captcha) = ?', $captcha)) {
    $password = sqAES::decrypt(sqSession::token(), $_POST['np']);
    $password = sqTools::hasher(hash('sha256', $user['email'].$password));
    sqSession::clearToken();
    sqTools::jStatus($SQ->DB()->PExecute('UPDATE sq_users SET password=?, captcha="", status=1 WHERE uid=?', $password, $user['uid']));
  } else {
    sqTools::jStatus();
  }
}

/**
 * set the vars needed for the page
 */
$tpl['js'] = array('vendor/gibberish-aes.min.js', 'vendor/jquery.blockUI.js', 'vendor/jquery.validate.min.js', 'vendor/jquery.sha1.js', 'RP.js');
$tpl['token'] = sqSession::token();
$tpl['captcha'] = $captcha;
