<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * switch - /slashquery/xx-10-32/switch.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

list($code, $expiry, $captcha) = explode('-', $SQ->router->query);

$tpl = $SQ->TPL();
/**
 * if not a valid captcha, display the Expired page
 */
$code = sqTools::validCaptchaXX1032($expiry, $captcha) ? $code : 'EX';

switch ($code) {
  /**
   * Reset Password
   */
  case 'RP':
    require_once 'RP.php';
    $tpl['code'] = 'RP.tpl';
    break;

  /**
   * New User
   */
  case 'NU':
    require_once 'NU.php';
    $tpl['code'] = 'NU.tpl';
    break;

  /**
   * Invalid confirmation code. The code may have expired.
   */
  case 'EX':
  default :
    $tpl['code'] = 'invalid.tpl';
}

$customTpl = 'slashquery/sites/' . $SQ->router->site . '/templates/' . SQ_TEMPLATE_NAME . '/xx-10-32/';
if (file_exists(SQ_ROOT.$customTpl.$tpl['code'])) {
  $tpl->template = $customTpl;
}

$this->notify();
