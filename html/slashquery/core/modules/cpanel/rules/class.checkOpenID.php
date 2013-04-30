<?php
/**
 * cpanel - /slashquery/core/modules/cpanel/rules/class.checkOpenid.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqcheckOpenID extends sqFinalRule {

  protected function evaluate() {
		/**
		 * check for OpenID headers
	   */
		if (isset($_GET['openid_ns']) || sqTools::postVars('openid_identifier')) {
		  /**
			 * start the openid object and validate the response
			 */
			$oi = new sqOpenID();
			if ($oi->getResponse('openid_mode')) {
				if ($oi->validate()) {
				  $claimed_id = $oi->getClaimedID();
					/**
					 * save the claimed id
					 */
					$oi = rtrim(preg_replace('#^https?://#', '', $claimed_id), '/');

					/**
					 * in case off an error (duplicity) catch the exception and create a
					 * session error flag
					 */
					try {
						$this->DB()->PExecute('INSERT INTO sq_users_openids (uid,openid) VALUES(?,?)', sqSession::Get('uid'), $oi);
					} catch (Exception $e) {
            sqSession::Set('error', 1);
					}
					header('Location: /cpanel/uopenid');
					exit;
				} else {
					return false;
				}
			}
		} else {
			if (sqTools::postVars('oi')) {

				$oi = new sqOpenID();
			  if ($oi->setIdentity(trim($_POST['oi']))->Discover()) {
					sqTools::jStatus(array('openid' => $oi->Auth()));
				} else {
					sqTools::jStatus();
				}
			} else {
			  sqTools::jStatus();
			}
		}
  }

}
