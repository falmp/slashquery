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
        <a href="/cpanel/configuration/cache">cache</a>
      </li>
    </ul>
  </div>
</div>


<div id="caches" class="row">
	<?php foreach ($this['caches'] as $cache => $stats) { ?>
  <div class="span6">
		<h3><?=$cache?></h3>
    <?php
    if (is_array($stats)):
    foreach ($stats as $hosts => $info) {
      switch(true) {
        case is_array($info):
          echo '<ol class="caches">
                  <li>',$hosts,'
                    <ul class="caches">';
                    foreach ($info as $key => $stats) {
                      echo "<li>$key: $stats</li>";
                    }
          echo '</ul></li></ol>';
          break;

        default :
          echo "$hosts: $info<br>";
      }
    }
    endif;
    echo '</div>';
	} ?>

</div>
<div class="center">
  <button id="submitButton" type="button" class="btn btn-warning">Flush cache</button>
  <p>If sessions are stored on cache, logged users will lost their current session.</p>
</div>
