<?php
/**
 * cPanel - /slashquery/core/modules/configuration/commands/class.database.php
 *
 * show database status
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDatabase extends sqFinalCommand {

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
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'cdatabase.js');
      $tpl['command_tpl'] = 'database.tpl';

      $sum_rows = 0;
      $table_size = 0;
      $sum_table_size = 0;
      $sum_datafree = 0;
      $status = array();
      foreach ($this->DB()->getAll('SHOW TABLE STATUS') as $value) {
        if ($value['Engine'] == 'MyISAM') {
          $table_size = doubleval($value['Data_length']) + doubleval($value['Index_length']);
          $sum_table_size+= $table_size;
          $sum_datafree+= $value['Data_free'];
        }
        $sum_rows+= $value['Rows'];
        $status[] = array($value['Name'], $value['Rows'], sqTools::format_filesize($table_size), sqTools::format_filesize($value['Data_free']), $value['Data_free'], $value['Engine']);
      }
      $tpl['status'] = $status;
      $tpl['sum_name'] = count($status);
      $tpl['sum_rows'] = $sum_rows;
      $tpl['sum_size'] = sqTools::format_filesize($sum_table_size);
      $tpl['sum_datafree'] = sqTools::format_filesize($sum_datafree);

      $this->notify();
    }

  }

}
