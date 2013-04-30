<?php
/**
 * cPanel - /slashquery/core/modules/cpanel/commands/class.default.php
 *
 * default class used when there is no command
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDefault extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

    /**
     * if user already logged in show admin pannel
     */
		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;

			/**
			 * get the Access Control List for the logged user
			 */
			$ACL = $this->ACL()->ACL ?: false;

			if ($ACL) {
				if ($extensions = (new coreACL($this->router, $this->DB))->getCpanelModules(true)) {
			    $exts = array();
					foreach ($extensions as $ext) {
						if (in_array($ext['name'], array_keys($ACL))) {
							$exts[] = $ext;
						}
					}
					/**
					 * list of module extensions allowed for this user
					 */
					$tpl['extensions'] = $exts ?: false;
				}
			}

    } else {
			/**
			 * if user not authenticated set header 401
			 * this is usefull when using the nginx auth_request_module
			 * nginx example configuration: auth_request /cpanel;
			 *
			 * @see http://mdounin.ru/hg/ngx_http_auth_request_module
       */
			header('HTTP/1.1 401 Unauthorized');

			$tpl['token'] = sqSession::token();

			/**
			 * check if the client remote IP address is not in the abuse list
			 */
			if (sqAbuse::match()) {
				/**
				 * include google recaptcha lib
				 */
				require_once SQ_ROOT .'slashquery/core/vendor/recaptcha/recaptchalib.php';
        /**
				 * display the reCAPTCHA
				 */
				$tpl['abuse'] = true;
				$tpl['publickey'] = RECAPTCHA_PUBLIC_KEY;
			}
    }
    $this->notify();
  }

}
