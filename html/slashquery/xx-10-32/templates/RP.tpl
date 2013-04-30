<div class="centeredBlock" style="height: 300px;">
	<div class="centeredBlockDiv">
		<form action="?" method="post" id="resetPasswordForm"  name="resetPasswordForm" class="form-horizontal">
			<input type="hidden" id="token" name="token" value="<?php echo $this['token']; ?>">
			<input type="hidden" id="captcha" name="captcha" value="<?php echo $this->router->query; ?>">
			<legend>{t core}Reset Password{/t}</legend>

			<div class="control-group">
				<label class="control-label" for="p1">New password:</label>

				<div class="controls">
					<input type="password" id="p1" name="p1">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="p2">Confirm your password:</label>

				<div class="controls">
					<input type="password" id="p2" name="p2">
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Save changes</button>&nbsp; <button type="button" class="btn cancelButton">Cancel</button>
			</div>
		</form>

		<div id="RPSuccess" class="alert alert-success hide">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Well done!</strong> Your password has been successfully changed.
		</div>

		<div id="RPError" class="alert alert-error hide">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			Error changing password
		</div>

	</div>
</div>
