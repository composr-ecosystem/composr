{$REQUIRE_JAVASCRIPT,securitylogging}

<div data-tpl="securityScreen">
	{TITLE}

	<h2>{!FAILED_LOGINS}</h2>

	{FAILED_LOGINS}

	<h2>{!SECURITY_ALERTS}</h2>

	<p>
		{!SECURITY_PAGE_CLEANUP}
	</p>

	{ALERTS}

	{+START,IF,{$NEQ,{NUM_ALERTS},0}}
		<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<p class="proceed-button">
				<button class="btn btn-danger btn-scr js-click-btn-delete-add-form-marked-posts" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!DELETE}</button>
			</p>
		</form>
	{+END}
</div>
