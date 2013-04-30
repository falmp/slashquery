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
        <a href="/cpanel/ACL/modules">modules</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span6">
    <h3>Modules on site ("<?php echo $this->router->site; ?>")</h3>

    <table id="sModules" class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($this['site_modules'] as $module) {
      echo '<tr>
            <td>',$module['name'],'</td>
            <td>',$module['description'],'</td>
            <td><a id="module-',$module['id'],'" href="#">';
            echo $module['status'] ? '<i class="icon-status1">&nbsp;</i>' : '<i class="icon-status0">&nbsp;</i>';
      echo 	'</a></td></tr>';
      }
    ?>
      </tbody>
    </table>
  </div>

  <div class="span6">
    <h3>Control Panel modules</h3>

    <table id="cpModules" class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($this['cpanel_modules'] as $module) {
        echo '<tr>
              <td>',$module['name'],'</td>
              <td>',$module['description'],'</td>';
        if ( in_array($module['name'], array('ACL','configuration','cpanel','users')) ) {
          echo '<td><i class="icon-status1">&nbsp;</i></td></tr>';
        } else {
          echo '<td><a id="module-' , $module['id'] , '" href="#">';
          echo $module['status'] ? '<i class="icon-status1">&nbsp;</i>' : '<i class="icon-status0">&nbsp;</i>';
          echo '</a></td></tr>';
        }
      }
    ?>
      </tbody>
    </table>
  </div>

</div>
