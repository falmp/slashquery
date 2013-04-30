    <hr>
    <footer>
      <p class="fl"><?php echo $_SERVER['HTTP_HOST']; ?></p><p class="fr"><?php echo sqTools::dateISO8601Z(); ?></p>
    </footer>

  </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo $this->getPath(); ?>js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
		<script src="<?php echo $this->getPath(); ?>js/vendor/bootstrap.min.js"></script>
		<script src="<?php echo $this->getPath(); ?>js/plugins.js"></script>
		<?php

		if ( $this['js'] && is_array($this['js']) ) {
			foreach ($this['js'] as $js) {
				echo '<script type="text/javascript" src="',$this->getPath(),'js/',$js,'"></script>';
			}
		}

		?>
	</body>
</html>
