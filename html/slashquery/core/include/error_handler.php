<?php if (!defined('SQ_ROOT')) exit('No direct script access allowed');
/**
 * error handler - /slashquery/core/error_handler.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

error_reporting(-1);

/**
 * SQ_ENV must be set on nginx either in the vhost or in in the fastcgi_params
 * file, example: fastcgi_param  SQ_ENV debug;
 */
ini_set('display_errors', isset($_SERVER['SQ_ENV']));

if (LOG_ERRORS == true) {
  ini_set('log_errors', 'on');
  ini_set('error_log', LOG_ERRORS_FILE);
}

set_error_handler('myErrorHandler');

function myErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
  if (!(error_reporting() & $errno)) {
    // This error code is not included in error_reporting
    return;
  }
  $dash = str_repeat('-', strlen(REPORT_ERROR_MAIL_SUBJECT));
  $error = "$dash\n";
  $error.= REPORT_ERROR_MAIL_SUBJECT."\n";
  $error.= "$dash\n";
  $error.= "Number: $errno\n";
  $error.= "String: [ $errstr ]\n";
  $error.= "File:   [ $errfile ]\n";
  $error.= "Line:   $errline\n";
  $error.= "HOST: ". gethostname()."\n";
  $html = '<div style="font-family: Courier, monospace; background-color: #fff; border: solid red; padding:1em">' . str_replace(REPORT_ERROR_MAIL_SUBJECT, "<font color=\"red\">".REPORT_ERROR_MAIL_SUBJECT."</font>", str_replace("\n", '<br>', $error)) . '</div><br>';
  if (isset($_SERVER['SQ_ENV'])) {
    echo $html;
  }
  if (REPORT_ERRORS_VIA_EMAIL == true) {
    $mail = new sqMail(1);
    $mail->Send(REPORT_ERROR_MAIL_SUBJECT, array(REPORT_ERROR_MAIL_FROM => REPORT_ERROR_MAIL_FROMNAME), array(REPORT_ERROR_MAIL_TO), $error);
  }
  if (LOG_ERRORS == true) {
    $dash = str_repeat('-', 80) . PHP_EOL;
    $elog = @date('c') . " - [$_SERVER[HTTP_HOST] - $_SERVER[REMOTE_ADDR]] - ".gethostname().PHP_EOL.$dash;
    $elog .= "Error #: $errno". PHP_EOL;
    $elog .= "File: $errfile" . PHP_EOL;
    $elog .= "Line: $errline" . PHP_EOL;
    $elog .= "Errstr: $errstr". PHP_EOL;
    $elog .= '$_POST: ' . PHP_EOL . json_encode($_POST) . PHP_EOL;
    $elog .= '$_SERVER: ' . PHP_EOL . json_encode($_SERVER) . PHP_EOL;
    $elog .= $dash;
    error_log($elog, 3, LOG_ERRORS_FILE);
  }

  if ($errno == E_USER_ERROR) {
    exit(1);
  }

  /* Don't execute PHP internal error handler */
  return true;
}

set_exception_handler('myExceptionHandler');

function myExceptionHandler($exception) {

  $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

  $msg = sprintf(
    $msg,
    get_class($exception),
    $exception->getMessage(),
    $exception->getFile(),
    $exception->getLine(),
    $exception->getTraceAsString(),
    $exception->getFile(),
    $exception->getLine()
    );

  if (isset($_SERVER['SQ_ENV'])) {
    echo '<pre>',$msg,'</pre>';
  }

  if (REPORT_ERRORS_VIA_EMAIL == true) {
    $mail = new sqMail(1);
    $mail->Send(REPORT_ERROR_MAIL_SUBJECT, array(REPORT_ERROR_MAIL_FROM => REPORT_ERROR_MAIL_FROMNAME), array(REPORT_ERROR_MAIL_TO), $msg);
  }

  error_log($msg, 3, LOG_ERRORS_FILE);
}
