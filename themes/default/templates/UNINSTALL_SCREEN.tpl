<p>
	{!UNINSTALL_WARNING,{$TUTORIAL_URL*,uninstall},{$TUTORIAL_URL*,tut_moving}}
</p>

<form title="{!PRIMARY_PAGE_FORM}" action="uninstall.php" method="post">
	{$INSERT_FORM_POST_SECURITY}

	<div>
		<p class="lonely-label"><label for="given_password">{!PASSWORD}:</label></p>
		<input id="given_password" class="form-control" type="password" name="given_password" />
	</div>

	<p>
		<button class="btn btn-danger btn-scr" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!UNINSTALL}</button>
	</p>
</form>
