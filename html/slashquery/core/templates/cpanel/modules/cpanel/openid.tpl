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
        <a href="/cpanel/user"><?php echo sqSession::Get('email'); ?></a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/uopenid">OpenID</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span6">
    <form action="#" method="post" id="oiForm" name="oiForm">
      <fieldset>
        <legend>Create an OpenID login</legend> <label for="oi" class="control-label required">Enter your OpenID</label>

        <div class="input-prepend input-append controls" style="white-space: nowrap">
          <span class="add-on"><i class="icon-openid">&nbsp;</i></span><input type="text" name="oi" id="oi" placeholder="user.sign.io"> <button type="submit" class="btn btn-primary">Go!</button>
        </div>
      </fieldset>
    </form>

    <div id="Ferror" class="<?php echo ($this['error']) ? '' : 'hide'; ?>">
      <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php
        if ($this['error']) {
          echo 'Could not store your OpenID, possible duplicate record';
        } else {
          echo 'Unable to discover your provider, please verify your OpenID.';
        }
        ?>
      </div>
    </div>
  </div>

  <?php if($this['uOpenIDs']): ?>
  <div class="span6">
    <legend>Your OpenID logins</legend>

    <table class="table">
      <thead>
        <tr>
          <th>OpenID</th>
          <th>Created</th>
          <th>Options</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($this['uOpenIDs'] as $key => $val) {
          echo "<tr>
                  <td>$val[openid]</td>
                  <td>$val[cdate]</td>
                  <td><a href=\"#d-$val[id]\"><i class=\"icon-trash\">&nbsp;</i></a></td>
                </tr>";
        } ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>

<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="myModalLabel">Delete</h3>
  </div>
  <div class="modal-body">
    <p>These OpenID will be permanently deleted and cannot be recovered, once deleted you will not be available to login with it. Are you sure?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-danger" id="delete" value="">Delete</button>
  </div>
</div>

<span id="token" class="hide"><?php echo $this['token']; ?></span>
