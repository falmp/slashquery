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
        <a href="/cpanel/configuration/languages">languages</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span6">
    <legend>Current translations 'locale' files</legend>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>locale</th>
          <th>Options</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($this['locales'] as $key => $locale) {
        echo "<tr><td>$key - $locale</td><td>".
             '<a href="/cpanel/configuration/languages/'.$key.'"><i class="icon-pencil">&nbsp;</i></a>'.
             '&nbsp;&nbsp;'.
             '<a href="#d-'.$key.'"><i class="icon-trash">&nbsp;</i></a>'.
             '</td></tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
  <div class="span6">
    <form id="localeForm" class="form-horizontal" method="post">
      <legend>Create translation files '<?=$this->router->site?>/locale/xx/xx.php'</legend>
      <div class="control-group">
        <label class="control-label" for="inputEmail">Language</label>
        <div class="controls">
          <select class="help-inline" name="iso"><?=$this->htmlOptions($this['languages'])?></select>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn">Create locale</button>
        </div>
      </div>
    </form>
  </div>
</div>

<hr>

<div class="row">
  <div class="span12">
    Caution! When creating a translation  for a specific language, the 'locale/iso/iso.php' file, is stored on file system, therefore you have to replicate it  in all your instances, this is only for cases where your site is load balanced or distributed on the cloud for example.
  </div>
</div>

<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="myModalLabel">Delete</h3>
  </div>
  <div class="modal-body">
    <p>These file and its translations will be permanently deleted and cannot be recovered. Are you sure?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-danger" id="delete" value="">Delete</button>
  </div>
</div>

<span id="token" class="hide"><?=$this['token']?></span>
