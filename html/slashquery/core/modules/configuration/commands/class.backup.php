<?php
/**
 * cPanel - /slashquery/core/modules/configuration/commands/class.backup.php
 *
 * default class used when there is no command
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqBackup extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
			$tpl['command_tpl'] = 'backup.tpl';
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'cbackup.js');

      /**
       * parse the DSN to get mysql details
       */
      $dsn = sqTools::parseURL(DSN);
      $dsn['user'] = rawurldecode($dsn['user']);
      $dsn['pass'] = rawurldecode($dsn['pass']);
      $dsn['path'] = rawurldecode(substr($dsn['path'], 1));
      $backup = $dsn['path'] . '-' . gmdate('c') .'.sql.gz';

      /**
       * create the backup dir
       */
      $backup_dir = DB_BACKUP_DIR;

      if (!file_exists($backup_dir)) {
      	mkdir($backup_dir,0755,true);
      }

      $backup_dir = realpath($backup_dir);

      /**
       * guess the possible mysqldump path
       */
      $mysqldump_paths = array('/usr/local/mysql/bin/mysqldump', '/usr/local/bin/mysqldump', '/usr/bin/mysqldump');
      foreach ($mysqldump_paths as $path) {
        if ( file_exists($path) ) {
          $mysqldump = $path;
          break;
        }
      }

      /**
       * mysqldump command
       */
      $command = $mysqldump . ' --compress --hex-blob --opt --skip-comments --host='.$dsn['host'].' --port='.$dsn['port'].' --user='.$dsn['user'].' --password="'.$dsn['pass'].'" '.$dsn['path'].' | gzip -9 > '.$backup_dir."/$backup";

      $tpl['scommand'] = $command;

      /**
       * store the command on session so that late via javascript we can run it
       */
      sqSession::Set('backup', $command);

			$this->notify();
    }

  }

}
