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
        <a href="/cpanel/configuration/abuse">abuse</a>
      </li>
    </ul>
  </div>
</div>

<?php
	if ($this['abuseDB']):
?>
<div class="row">
	<div class="span10">
		<h3>Abuse DB</h3>
		<table class="table">
			<thead>
				<tr>
					<th>IP</th>
					<th>user</th>
					<th>User Agent</th>
					<th>Created Date (UTC)</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<?php
				while ($row = $this['abuseDB']->fetchArray(SQLITE3_ASSOC)) {
          echo '<tr><td>',$row['IP'] , '</td><td>' , $row['email'] , '</td><td>' , base64_decode($row['UA']) , '</td><td>' , $row['cdate'] , '</td><td><a href="#d-', $row['id'],'"><i class="icon-trash">&nbsp;</i></a></td></tr>';
        }
				?>
			</tbody>
		</table>
	</div>
	<div class="span2">
		<h3>Flush DB</h3>
		<p>Deleting the Abuse DB will clear the database allowing user to login with out having to enter a reCAPTCHA, in case of any abuse detected database will automatically start to get filled.</p>
		<button class="btn btn-warning" type="button" id="deleteBtn">Delete the abuse DB</button>
	</div>
</div>
<?php else: ?>
<h3>No Abuse DB found</h3>
<p>This are good news, meaning that your site users are behaving well in the sense that are not trying to login many times with erroneous passwords, or trying a brute force attack.</p>
<?php endif; ?>
