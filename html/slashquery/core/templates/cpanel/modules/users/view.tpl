<div class="row">
  <div class="span12">
    <ul class="breadcrumb">
      <li>
        <a href="/cpanel"><i class="icon-home">&nbsp;</i></a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel">cPanel</a> <span class="divider">/</span>
      </li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Users <b class="caret">&nbsp;</b></a> <span class="divider">/</span>
        <ul class="dropdown-menu">
          <li><a href="/cpanel/users/add">Add user</a></li>
        </ul>
      </li>
      <li>
        <a href="/cpanel/users/">View</a>
      </li>
    </ul>
  </div>
</div>


<form class="form-search pull-right">
  <div class="input-append">
    <input type="text" class="span2 search-query" placeholder="Search name or email">
    <button type="submit" class="btn">Search</button>
  </div>
</form>

<table class="table table-striped">
  <caption>Total: <span class="badge badge-info"><?php echo $this['tusers']; ?></span>&nbsp;&middot;&nbsp;Active: <span class="badge badge-success"><?php echo $this['active']; ?></span>&nbsp;&middot;&nbsp;Inactive: <span class="badge"><?php echo $this['inactive']; ?></span></caption>
  <thead>
    <tr>
      <th><a id="pSort-name" href="#">Name</a></th>
      <th><a id="pSort-email" href="#">Email</a></th>
      <th><a id="pSort-cdate" href="#">Created</a></th>
      <th>Roles</th>
      <th class="center">Options</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this['users'] as $key => $user) { ?>
    <tr>
      <td><?php echo $user['name']; ?></td>
      <td><?php echo $user['status'] ? $user['email'] : "<del><em>$user[email]</em></del>"; ?></td>
      <td><?php echo $user['cdate']; ?></td>
      <td><?php echo $user['roles'] ? str_replace('|', '<br>', htmlentities($user['roles'], ENT_QUOTES | ENT_IGNORE, 'UTF-8')) : ''; ?></td>
      <td><a href="/cpanel/users/edit/<?php echo $user['uid']; ?>"><i class="icon-pencil">&nbsp;</i></a>
      <?php if ($user['uid'] != 1) { ?>
      &nbsp;&nbsp;&nbsp;<a href="#d-<?php echo $user['uid']; ?>"><i class="icon-trash">&nbsp;</i></a></td>
      <?php } ?>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php if ($this['tusers'] > 50): ?>
<div class="pagination pagination-centered">
  <ul id="paginator">
    <li class="active"><a href="#p-1">1</a></li>
    <?php
      for ($i=2; $i <= ($this['Tpages'] > 7 ? 7 : $this['Tpages']); $i++) {
        echo "<li><a href=\"#p-$i\">$i</a></li>";
      } if ($this['Tpages'] > 7) {
        echo '<li><a href="#next">Next »</a></li>';
      }
    ?>
  </ul>
</div>

<div class="center">
  <p>Page <span id="cPage">1</span> of <span id="Tpages"><?php echo $this['Tpages']; ?></span></p>
</div>
<?php endif; ?>

<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="myModalLabel">Delete</h3>
  </div>
  <div class="modal-body">
    <p>These user will be permanently deleted and cannot be recovered. Are you sure?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-danger" id="delete" value="">Delete</button>
  </div>
</div>

<span id="token" class="hide"><?php echo $this['token']; ?></span>
