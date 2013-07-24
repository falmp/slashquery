<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.resetPassword.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqResetPassword extends sqFinalRule {

  protected function evaluate() {

    /**
     * normal auth using email, password, token
     */
    if (sqTools::postVars('email2','token') && sqSession::validToken($_POST['token'], 'token', false) && sqTools::validEmail($_POST['email2'])) {
      $to_email = trim($_POST['email2']);
      $captcha = sqTools::captchaXX1032();
      $user = new coreUser($this->DB);
      /**
       * @var $rs string status saved on DB, RP = 2, NU = 0 or false
       */
      $rs = $user->resetCaptcha($to_email, $captcha);
      if ($rs) {
        /**
         * data is obtained from the table sq_config, the FROM field needs to
         * contain a valid email otherwise defaults to no-reply@slashquery.org
         */
        $from_email = sqTools::validEmail($from_email = str_replace('__HOST__', $_SERVER['HTTP_HOST'], $user->sqConfig('cpanel_email_resetPassword_from'))) ? $from_email : 'no-reply@slashquery.org';
        $from_name = str_replace('__SITE__', $this->router->site, $user->sqConfig('cpanel_email_resetPassword_from_name'));

        $from = array($from_email => $from_name);
        $to = array($to_email);
        $subject = str_replace('__SITE__', $this->router->site, $user->sqConfig('cpanel_email_resetPassword_subject'));
        $body = str_replace('\n', "\n", sqTools::trimNS($user->sqConfig('cpanel_email_resetPassword_body')));

        /**
         * send the email
         */
        try {
          sqTools::jStatus(sqTools::sendXX1032($rs, $captcha, $from, $to, $subject, $body));
        } catch (Exception $e) {
          sqTools::jStatus($e);
        }
      } else {
        sqTools::jStatus();
      }
    } else {
      sqTools::jStatus();
    }
  }

}
