<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * sqMail - /slashquery/core/classes/class.sqMail.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @see http://swiftmailer.org/
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

require_once SWIFT_MAILER;

class sqMail extends sqBase {
  private $mailer;

  /**
   * sqMail if $sqServer use SQ constants
   *
   * @param int $sqServer
   */
  public function __construct($sqServer=null) {

    switch (true) {
      case $sqServer:
        $transport = Swift_SmtpTransport::newInstance(SQ_SMTP_HOST, SQ_SMTP_PORT, SQ_SMTP_SECURE)->setUsername(SQ_SMTP_USERNAME)->setPassword(SQ_SMTP_PASSWORD);
        break;

      default :
        $transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT, SMTP_SECURE)->setUsername(SMTP_USERNAME)->setPassword(SMTP_PASSWORD);
    }

    $this->mailer = Swift_Mailer::newInstance($transport);
  }

  /**
   * Send email
   *
   * @param string $subject
   * @param array $from  - array (email => name)
   * @param array $to    - array(email1, email2)
   * @param string $body - string
   * @param array $cheaders - array(custom => header)
   * @return boolean
   */
  public function Send($subject = null, array $from = array(), array $to = array(), $body = null, array $cheaders = array()) {
    $subject = $subject ?: $this->Subject;
    $from    = $from ?: $this->From;
    $to      = $to ?: $this->To;
    $body    = $body ?: $this->Body;
    $message = Swift_Message::newInstance()->setSubject($subject)->setFrom($from)->setTo($to)->setBody($body);

    if ($cheaders) {
      $headers = $message->getHeaders();
      foreach ($cheaders as $key => $value) {
        $headers->addTextHeader($key, $value);
      }
    }

    return (empty($from) || empty($to)) ? false : $this->mailer->send($message);
  }

}
