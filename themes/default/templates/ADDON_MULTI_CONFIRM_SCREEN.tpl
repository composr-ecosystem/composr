{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{WARNINGS}

{+START,IF_NON_EMPTY,{INSTALL_FILES}}
	<p class="lonely-label">{!ADDON_FILES}:</p>
	<ul>
		{INSTALL_FILES}
	</ul>
{+END}

{+START,IF_NON_EMPTY,{UNINSTALL_FILES}}
	<p>{!WARNING_UNINSTALL_GENERAL}</p>

	<p>{!WARNING_UNINSTALL}</p>

	<ul>
		{UNINSTALL_FILES}
	</ul>
{+END}

<div class="clearfix">
	<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" data-submit-modsecurity-workaround="1">
		{$INSERT_FORM_POST_SECURITY}

		{HIDDEN}

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--back" type="button" data-cms-btn-go-back="1">{+START,INCLUDE,ICON}NAME=buttons/back{+END} <span>{!GO_BACK}</span></button>

			<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{!PROCEED} {+START,INCLUDE,ICON}NAME=buttons/proceed{+END}</button>
		</p>
	</form>
</div>
