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
        <a href="/cpanel/ACL">ACL</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/ACL/permissions">permissions</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span12">

<?php if ($this['roles']) { ?>
<div class="btn-group">
  <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Select a role <span class="caret">&nbsp;</span></button>
  <ul class="dropdown-menu">
		<?php
		foreach ($this['roles'] as $rid => $role) {
			echo "<li><a href=\"#r-$rid\">$role</a></li>";
		}
		?>
  </ul>
</div>
<hr>
<?php } ?>

  <form action="/cpanel/ACL/permissions/default" method="post" class="well">
    <input type="hidden" name="token" value="<?=$this['token']?>">
    <table class="table perms">
      <thead>
        <tr>
          <th>Modules</th>
          <th>Anonymous users</th>
          <th>Authenticated users</th>
        </tr>
      </thead>
<?php
foreach ($this['modules'] as $type => $values) {
	foreach ($values as $module) {
		echo '<tbody>
            <tr>
            	<td><a class="moduleTitle" href="#">',strtoupper($module['name']),': ',$module['description'],'</a></td>
            	<td>'.ucfirst($type).'</td>
            	<td>'.ucfirst($type).'</td>
            </tr>
          </tbody>

					<tbody id="',strtoupper($module['name']),'"';	if ($type == 'cpanel') { echo ' class="hide"'; } echo '>';
		foreach ($module['ACL'] as $permID => $perm) {
			echo '<tr>
							<td>';
							$perm1 = array_shift($perm);
							echo $perm1;
							echo !empty($perm) ? ' - <i>'.implode(', ', $perm).'</i></td>' : '</td>';
				echo '<td>';
					if ($type != 'cpanel') {
					echo '<input type="checkbox" name="permissions[1][',$type,'][',$module['name'],'][]" value="',$permID,'"';
						if (isset($this['ACLs'][1][$type][$module['name']][$permID])) { echo ' checked="checked"'; } echo '>';
					}
				echo '</td>
							<td>
				      <input type="checkbox" name="permissions[2][',$type,'][',$module['name'],'][]" value="',$permID,'"';
						if (isset($this['ACLs'][2][$type][$module['name']][$permID])) { echo ' checked="checked"'; } echo '></td>';
			echo '</tr>';
		}
		echo '</tbody>';
	}
}
?>
    </table>
    <div class="center">
      <button id="sform" type="submit" class="btn btn-warning">Save permissions</button>
    </div>
  </form>
  </div>
</div>
