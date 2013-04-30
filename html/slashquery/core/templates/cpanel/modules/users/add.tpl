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
        <a href="/cpanel/users">Users</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/users/add">Add</a>
      </li>
    </ul>
  </div>
</div>

<h3>Create a new user</h3>

<div id="Fsuccess" class="alert alert-success hide">
  <a class="close" href="#">&times;</a>
  <h4 class="alert-heading">User created</h4>
  To view your users visit: <a href="/cpanel/users">Manage users</a>
</div>

<div id="Ferror" class="alert alert-error hide">
  <a class="close" href="#">&times;</a>
  <strong>Error creating user</strong>
</div>

<div class="row">
  <div class="span12">
    <form accept-charset="UTF-8" id="addUser" name="addUser" method="post" action="/cpanel/users/add" class="form-horizontal">
      <fieldset>
        <input name="utf8" type="hidden" value="&#x2713;">
        <input type="hidden" id="token" name="token" value="<?php echo $this['token']; ?>">

        <div class="control-group">
          <label class="control-label" for="container">{t core}Name{/t}</label>
          <div class="controls">
            <input id="name" name="name" type="text" class="input-xlarge" maxlength="128" placeholder="new user full name"/>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label required" for="container">{t core}Email{/t}</label>
          <div class="controls">
            <input id="email" name="email" type="text" class="input-xlarge" maxlength="128" placeholder="e-mail address"/>
          </div>
        </div>

        <div class="control-group oitoggle">
          <label class="control-label required" for="container">{t core}Password{/t}</label>
          <div class="controls">
            <input id="p1" name="p1" type="password" class="input-xlarge"  />
          </div>
        </div>

        <div class="control-group oitoggle">
          <label class="control-label required" for="container">{t core}Confirm password{/t}</label>
          <div class="controls">
            <input id="p2" name="p2" type="password" class="input-xlarge"  />
          </div>
        </div>

        <div class="control-group">
          <label class="control-label required" for="container">{t core}Gender{/t}</label>
          <div class="controls">
            <label class="radio">
              Male<input id="gender1" name="gender" type="radio" value="1" />
            </label>
            <label class="radio">
              Female<input id="gender2" name="gender" type="radio" value="2" />
            </label>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label required" for="container">{t core}Status{/t}</label>
          <div class="controls">
            <label class="radio">
             Active<input id="status1" name="status" type="radio" value="1" />
            </label>
            <label class="radio">
             Inactive<input id="status2" name="status" type="radio" value="0" />
            </label>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="container">{t core}Roles{/t}</label>
          <div class="controls">
          <?php foreach ($this['roles'] as $rid => $name) { ?>
            <label class="checkbox">
              <input type="checkbox" name="roles" value="<?php echo $rid; ?>" /><?php echo $name; ?>
            </label>
          <?php } ?>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Add user</button>&nbsp;<a href="/cpanel/users">Cancel</a>
        </div>

      </fieldset>
    </form>
  </div>
</div>
