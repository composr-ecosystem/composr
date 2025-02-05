{$REQUIRE_JAVASCRIPT,core_notifications}

{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<form title="{!NOTIFICATIONS}" method="post" action="{ACTION_URL*}">
	{$INSERT_FORM_POST_SECURITY}

	<input type="hidden" name="submitting" value="1" />

	<div>
		{INTERFACE}

		<p class="proceed-button">
			<button type="submit" class="btn btn-primary btn-scr buttons--save">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
		</p>
	</div>
</form>
