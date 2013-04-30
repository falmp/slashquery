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
        <a href="/cpanel/ACL/roles">roles</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span6">
    <h3>Current roles</h3>

    <table id="roles" class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Options</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($this['roles'] as $role) {
        echo '<tr>
              <td>',$role['name'],'</td>';
        if ($role['rid'] > 3) {
          echo '<td><a href="#e-'.$role['rid'].'"><i class="icon-pencil">&nbsp;</i></a>&nbsp;&nbsp;&nbsp;<a href="#d-'.$role['rid'].'"><i class="icon-trash">&nbsp;</i></a></td></tr>';
        } else {
          echo '<td></td></tr>';
        }
      }
      ?>
      </tbody>
    </table>
  </div>

  <div class="span6">

  <h3>Create a role</h3>

    <div id="createRoleSuccess" class="alert alert-success hide">
      <a class="close" href="#">&times;</a>
      <strong>Role created</strong>
    </div>

    <div id="modifyRoleSuccess" class="alert alert-success hide">
      <a class="close" href="#">&times;</a>
      <strong>Role modified</strong>
    </div>

    <div id="createRoleError" class="alert alert-error hide">
      <a class="close" href="#">&times;</a>
      <strong>Error creating role</strong>
    </div>


    <form id="createRole" method="post" action="/cpanel/ACL/roles/" class="form-horizontal">
      <input id="rid" type="hidden" name="rid" value="0" />
      <div class="control-group">
        <label class="control-label required" for="role">Role</label>
        <div class="controls">
          <input type="text" id="role" name="role" placeholder="Role name">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" id="submitButton">Create role</button>&nbsp;&nbsp;
        <button type="button" class="btn hide" id="cancelButton">Cancel</button>
      </div>
    </form>
 </div>

</div>

<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="myModalLabel">Delete</h3>
  </div>
  <div class="modal-body">
    <p>These role will be permanently deleted and cannot be recovered. Are you sure?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-danger" id="delete" value="">Delete</button>
  </div>
</div>

<span id="token" class="hide"><?=$this['token']?></span>
