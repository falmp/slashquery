    <hr>
    <footer>
      <p class="fl">Site: <?php echo $this->router->site; ?></p><p class="fr"><?php echo sqTools::dateISO8601Z(); ?></p>
    </footer>

  </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo $this->getPath(); ?>js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/bootstrap.min.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/plugins.js"></script>
		<?php

		if ( $this['js'] && is_array($this['js']) ) {
			foreach ($this['js'] as $js => $val) {
				if ($val == 'ext') {
					echo '<script type="text/javascript" src="',$this->getPath(1),'js/',$js,'"></script>';
				} else {
					echo '<script type="text/javascript" src="',$this->getPath(0),'js/',$val,'"></script>';
				}
			}
		}

		?>
	</body>
</html>
