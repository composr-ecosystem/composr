{$REQUIRE_JAVASCRIPT,securitylogging}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div data-tpl="securityScreen">
	{TITLE}

	<h2>{!SECURITY_ALERTS}</h2>

	<p>
		{!SECURITY_PAGE_CLEANUP}
	</p>

	{+START,IF_PASSED,FILTERCODE_BOX}
		{+START,INCLUDE,FILTER_BOX}{+END}
	{+END}

	{ALERTS}

	{+START,IF,{$NEQ,{NUM_ALERTS},0}}
		<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<p class="proceed-button">
				<button class="btn btn-danger btn-scr js-click-btn-delete-add-form-marked-posts" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE}</span></button>
			</p>
		</form>
	{+END}

	<h2>{!FAILED_LOGINS}</h2>

	{FAILED_LOGINS}
</div>
