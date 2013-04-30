<?php
/**
 * sqView - /slashquery/core/classes/class.sqView.php
 * Observer
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqView implements SplObserver {

  public function update(SplSubject $subject) {

    $tpl = $subject->SQ->TPL();

    if (isset($tpl->ignore)) {
      return;
    }

   /**
    * if showError is set, display a custom error
    */
    if (isset($tpl->showError)) {
      $this->showError($tpl->showError, $tpl);
    } else {
      $command_tpl = is_null($tpl->router->command) ? 'null' : $tpl->router->command;
      $command_tpl = $tpl['command_tpl'] ?: $command_tpl . '.tpl';

      $tpl->module_tpl =  'modules/' . $tpl->router->module . '/' . ($tpl['module_tpl'] ?: $tpl->router->module . '.tpl');
      $tpl->command_tpl = 'modules/' . $tpl->router->module . '/' . $command_tpl;

      sqLogger::log(__CLASS__, "tpl: $tpl->template", "module: $tpl->module_tpl", "command: $tpl->command_tpl");

      $tpl->display($tpl->template . 'base.tpl');
    }

    return true;
  }

  /**
   * showError - display custom error based on the code
   *
   * @param int $code
   * @param sqTPL $tpl
   */
  public function showError($code = null, sqTPL $tpl) {
    if (file_exists($tpl->template . 'errors/hook.tpl')) {
      $tpl->display($tpl->template . 'errors/hook.tpl');
    } else {
      switch ($code) {
        case 1: // module disabled
          $tpl->display($tpl->template . 'errors/moduleDisabled.tpl');
          break;
        default :
          $tpl->display($tpl->template . 'errors/accessDenied.tpl');
      }
    }
  }

}
