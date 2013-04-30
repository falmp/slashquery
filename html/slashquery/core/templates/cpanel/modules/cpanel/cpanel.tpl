<div class="row">
  <div class="span12">
    <ul class="breadcrumb">
      <li>
        <a href="/cpanel"><i class="icon-home">&nbsp;</i></a> <span class="divider">/</span>
      </li>
    </ul>
  </div>
</div>

<?php
  if ($this->ACL()->usertype == 3):
?>
<div class="row">
  <div class="span4">
    <h2>Users</h2>
     <ul class="nav nav-list">
      <li><a href="/cpanel/users/add"><i class="icon-user">&nbsp;</i> Add user</a></li>
      <li><a href="/cpanel/users"><i class="icon-search">&nbsp;</i> View users</a></li>
     </ul>
  </div>
  <div class="span4">
    <h2>Access control</h2>
    <ul class="nav nav-list">
      <li><a href="/cpanel/ACL/modules"><i class="icon-th">&nbsp;</i> Modules</a></li>
      <li><a href="/cpanel/ACL/permissions"><i class="icon-lock">&nbsp;</i> Permissions</a></li>
      <li><a href="/cpanel/ACL/roles"><i class="icon-th-list">&nbsp;</i> Roles</a></li>
    </ul>
  </div>
  <div class="span4">
    <h2>Configuration</h2>
    <ul class="nav nav-list">
      <li><a href="/cpanel/configuration/languages"><i class="icon-flag">&nbsp;</i> Languages</a></li>
      <li><a href="/cpanel/configuration/cache"><i class="icon-refresh">&nbsp;</i> Flush cache</a></li>
      <li><a href="/cpanel/configuration/database"><i class="icon-heart">&nbsp;</i> Database</a></li>
      <li><a href="/cpanel/configuration/backup"><i class="icon-hdd">&nbsp;</i> Backup DB</a></li>
      <li><a href="/cpanel/configuration/abusedb"><i class="icon-exclamation-sign">&nbsp;</i> Abuse DB</a></li>
    </ul>
  </div>
</div>
<?php
  endif;
?>

<?php if ($this['extensions']): ?>
<div class="row">
  <div class="span4">
    <h2>Extensions</h2>
    <ul class="nav nav-list">
      <?php
      foreach ($this['extensions'] as $key =>$ext) {
        if ($ext['status']) {
          echo '<li><a href="/cpanel/' . $ext['name'] . '"><i class="icon-' . ($ext['cpanel'] == 2 ? 'asterisk' : 'th-large') . '">&nbsp;</i>'. $ext['name'].' - ' . $ext['description'] . '</a></li>';
        }
      }
      ?>
    </ul>

  </div>
</div>
<?php endif; ?>
