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
      <a href="/cpanel/user"><?php echo sqSession::Get('email'); ?></a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span12">
    <h3>Last Login Information</h3>
    <dl class="dl-horizontal">
      <dt>Date (UTC)</dt>
      <dd><?=$this['ulog']['cdate']?></dd>

      <dt>IP</dt>
      <dd><?=$this['ulog']['ip']?></dd>

      <dt>Host</dt>
      <dd><?=$this['ulog']['host']?></dd>

      <dt>User Agent</dt>
      <dd><?=$this['ulog']['ua']?></dd>

      <dt>Login count</dt>
      <dd><?=sqSession::Get('lcount')?></dd>
    </dl>
  </div>
</div>

<div class="row">
  <div class="span6">
    <h3>User details</h3>
    <form accept-charset="UTF-8" id="changeDetails" name="changeDetails" method="post" action="#" class="form-horizontal">
      <fieldset>
        <input name="utf8" type="hidden" value="&#x2713;">
        <input type="hidden" name="token" value="<?php echo $this['token']; ?>">

        <div class="control-group">
          <label class="control-label" for="container">{t core}Name{/t}</label>
          <div class="controls">
            <input id="name" name="name" type="text" class="input-xlarge" maxlength="128" value="<?=$this['uDetails']['name']?>">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="container">{t core}Gender{/t}</label>
          <div class="controls">
            <label class="radio">
              Male<input name="gender" type="radio" value="1"  <?php if ($this['uDetails']['sex'] == 1) echo 'checked'; ?>>
            </label>
            <label class="radio">
              Female<input name="gender" type="radio" value="2" <?php if ($this['uDetails']['sex'] == 2) echo 'checked'; ?>>
            </label>
          </div>
        </div>

        <div class="form-actions hide">
          <button type="submit" class="btn btn-primary">Update</button>&nbsp;
          <button type="reset" class="btn cancel">Cancel</button>
        </div>
      </fieldset>
    </form>
  </div>


  <div class="span6">
    <h3>Authentication options</h3>
    <button id="cpBtn" type="button" class="btn"><i class="icon-lock">&nbsp;</i> Change your password</button>

    <form accept-charset="UTF-8" id="changePass" name="changePass" method="post" action="#" class="form-horizontal hide">
      <fieldset>
        <input type="hidden" name="token" value="<?php echo $this['token']; ?>">

        <div class="control-group oitoggle">
          <label class="control-label required" for="container">{t core}Password{/t}</label>
          <div class="controls">
            <input id="p1" name="p1" type="password" class="input-xlarge" >
          </div>
        </div>

        <div class="control-group oitoggle">
          <label class="control-label required" for="container">{t core}Confirm password{/t}</label>
          <div class="controls">
            <input id="p2" name="p2" type="password" class="input-xlarge">
          </div>
        </div>

        <div class="controls">
          <button type="submit" class="btn btn-warning">Change password</button>&nbsp;<button type="reset" class="btn cancel">Cancel</button>
        </div>
      </fieldset>
    </form>
    <div id="cpFsuccess" class="alert alert-success hide">
      Your password has been successfully changed
    </div>

    <hr>

    <a href="/cpanel/uopenid/" class="btn"><i class="icon-openid">&nbsp;</i> OpenID login</a>
  </div>

</div>
