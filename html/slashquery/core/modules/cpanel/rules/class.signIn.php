<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.signIn.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSignIn extends sqFinalRule {

  protected function evaluate() {

    /**
     * try openid if and oi (openid_indentifier) is posted
     */
    if (isset($_GET['openid_ns']) || sqTools::postVars('openid_identifier')) {
      /**
       * try to authenticate using openid
       */
      $oi = new sqOpenID();
			if ($oi->getResponse('openid_mode')) {
				if ($oi->validate()) {
          $auth = new sqAuth($this->DB);
          $auth->loginOpenID($oi->getClaimedID(), true);
				}
        header('Location: /cpanel');
        exit;
      }
    } else {
      /**
       * normal auth using email, password, token
       */
      if (sqTools::postVars('e','p','t','r')) {

        /**
         * if not a valid token, increment the login counter and reload page
         */
        if (!sqSession::validToken($_POST['t'], 'token', false)) {
          sqSession::Set('lerror', 1 + sqSession::Get('lerror'));
          sqTools::jStatus('r');
        }

        if (sqTools::postVars('oi')) {
          $oi = new sqOpenID();
          sqTools::jStatus( $oi->setIdentity(trim($_POST['oi']))->Discover() ? array('openid' => $oi->Auth()) : false);
        }

        /**
         * reCAPTCHA parameters
         * rc = recaptcha_challenge_field
         * rr = recaptcha_response_field
         * @see https://developers.google.com/recaptcha/docs/php
         */
        if (sqTools::postVars('rc')) {
          require_once SQ_ROOT .'slashquery/core/vendor/recaptcha/recaptchalib.php';
          $resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, sqTools::getIPv4(), $_POST['rc'], $_POST['rr']);
          if (!$resp->is_valid) {
            /**
             * The reCAPTCHA wasn't entered correctly. Go back and try it again.
             */
            sqTools::jStatus();
          }
        }

        /**
         * authenticate using the posted data
         * email and password must be decrypted before
         */
        $auth = new sqAuth($this->DB);
        $auth->token    = $_POST['t'];
        $auth->email    = sqAES::decrypt(sqSession::token(), $_POST['e']);
        $auth->password = sqAES::decrypt(sqSession::token(), $_POST['p']);
        if ($_POST['r'] == 'true') {
          $auth->remember = true;
        }

        /**
         * forcelogin
         */
        if (isset($_POST['f']) && $_POST['f'] == 'true') {
          $auth->forcelogin = true;
        }

        /**
         * $auth->login(true) "true" will store the user login history on session
         */
        if ($rs = $auth->login(true)) {
          sqTools::jStatus($rs);
        } else {
          /**
           * increment login error in 1
           */
          sqSession::Set('lerror', 1 + sqSession::Get('lerror'));
          /**
           * add user the the Abuse DB and show reCAPTCHA after 3 failed attempts
           */
          if (sqSession::Get('lerror') > 3) {
            sqAbuse::Add($auth->email);
            sqTools::jStatus('r');
          }
          sqTools::jStatus();
        }
      } else {
        sqSession::Set('error', 1 + sqSession::Get('error'));
        sqTools::jStatus();
      }
    }

  }

}
