<div class="row">
  <div class="span12">
    <ul class="breadcrumb">
      <li>
        <a href="/cpanel"><i class="icon-home">&nbsp;</i></a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel">cPanel</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/configuration/">Configuration</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/configuration/database">database</a>
      </li>
    </ul>
  </div>
</div>

<h3>Database status</h3>

<table class="table table-striped">
	<thead>
    <tr>
      <th>Table name</th>
		  <th>Type</th>
			<th>Rows</th>
			<th>Size</th>
			<th>Overhead</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($this['status'] as $db) {
	  if ($db[4] > 0 && $db[5] == 'MyISAM') {
			echo "<tr class=\"error\">
              <td>$db[0]</td>
							<td>$db[5]</td>
							<td>$db[1]</td>
							<td>$db[2]</td>
							<td>$db[3]</td>
            </tr>";
		} else {
			echo "<tr>
            <td>$db[0]</td>
						<td>$db[5]</td>
						<td>$db[1]</td>
					  <td>$db[2]</td>
						<td>";
						echo $db[5] == 'InnoDB' ? '<i>'.$db[3].'</i>' : $db[3];
      echo '</td></tr>';
		}
	}
?>
  </tbody>
  <tfoot>
    <tr>
      <th >Total: <?=$this['sum_name']?></th>
      <th></th>
      <th><?=$this['sum_rows']?></th>
      <th><?=$this['sum_size']?></th>
      <th><?=$this['sum_datafree']?></th>
    </tr>
  </tfoot>
</table>

<div class="center">
  <button id="submitButton" type="button" class="btn btn-success">Optimize Database</button>
</div>
