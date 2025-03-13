{TITLE}

{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{RESULTS_TABLE}

<div class="buttons-group">
	<div class="buttons-group-inner">
		<form title="{!DELETE_ALL}" class="right" action="{MASS_DELETE_URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<div class="inline">
				<button class="btn btn-danger btn-scr" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE_ALL}</span></button>
			</div>
		</form>
		<form title="{!SEND_ALL}" class="right" action="{MASS_SEND_URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<div class="inline">
				<button class="btn btn-primary btn-scr buttons--send" type="submit">{+START,INCLUDE,ICON}NAME=buttons/send{+END} <span>{!SEND_ALL}</span></button>
			</div>
		</form>
	</div>
</div>
