<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * New User - /slashquery/xx-10-32/NU.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

switch (true) {

  default :
    if ($user = $SQ->DB()->FetchMode('ASSOC')->PGetRow('SELECT uid, name FROM sq_users WHERE status=0 AND HEX(captcha) = ?', $captcha)) {
      if ($SQ->DB()->PExecute('UPDATE sq_users SET captcha="", status=1 WHERE uid=?', $user['uid'])) {
        $tpl['user_name'] = $user['name'];
        $tpl['status'] = 'confirmed';
      }
    } else {
      $tpl['status'] = 'invalid';
    }
}
