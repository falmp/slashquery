<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/users/rules/class.pagination.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqPagination extends sqFinalRule {

  protected function evaluate() {

    /**
     * check for page
     */
    if (sqTools::postVars('page')) {
      $page = $_POST['page'];
      $rp 	= 50;

      $start = ($page-1) * $rp;
      $sort = "LIMIT $start, $rp";

    	/**
       * @var array users
       */
      $users = $this->DB()->FetchMode('ASSOC')->getAll("SELECT t1.uid, t1.name, t1.email, t1.cdate, t1.status, group_concat(t3.name ORDER BY t3.name ASC SEPARATOR '|') AS roles FROM sq_users t1 LEFT JOIN sq_users_roles t2 USING(uid) LEFT JOIN sq_roles t3 USING (rid) GROUP BY uid ORDER BY t1.uid DESC $sort");

  		/**
       * @var int total users
       */
      $total = $this->DB()->GetOne('SELECT COUNT(uid) FROM sq_users');

      $data = array();
      $data['page'] = $page;
      $data['Tpages'] = ceil($total / $rp);

      if (is_array($users)) {
      	foreach ($users as $key => $row) {
      		$icons = '<a href="/cpanel/users/edit/'.$row['uid'].'"><i class="icon-pencil"></i></a>';
      		if ($row['uid'] != 1) {
      			$icons .= '&nbsp;&nbsp;&nbsp;<a href="#d-'.$row['uid'].'"><i class="icon-trash"></a>';
      		}
      		$email = ($row['status']) ? $row['email'] : "<del><em>$row[email]</em></del>";
      	  $data['rows'][] = array(
      	    'cell' => array($row['name'], $email, $row['cdate'], ( ($row['roles']) ? str_replace('|', '<br>', htmlentities($row['roles'], ENT_QUOTES | ENT_IGNORE, 'UTF-8')) : ''), $icons)
          );
        }
      } else {
      	$data['rows'] = false;
      }
      sqTools::jStatus($data);
    } else {
      sqTools::jStatus();
    }
  }

}
