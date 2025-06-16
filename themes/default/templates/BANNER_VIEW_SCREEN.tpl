{TITLE}

{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<h2>{!DETAILS}</h2>

{MAP_TABLE}

<h2>{!VIEW}</h2>

<p>
	{!BANNER_LOOKS}
</p>

{$PARAGRAPH,{BANNER}}

{+START,IF_NON_EMPTY,{RESULTS_TABLE}}
	<h2>{!_STATISTICS}</h2>

	{RESULTS_TABLE}
{+END}

{+START,IF_NON_EMPTY,{RESET_URL}}
	<form title="{!RESET_BANNER_STATS}" action="{RESET_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<input type="hidden" name="confirm" value="1" />
		<button class="btn btn-danger btn-scr" type="submit" data-cms-confirm-click="{!WARNING_RESET_BANNER_STATS}">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!RESET_BANNER_STATS}</span></button>
	</form>
{+END}

{$REVIEW_STATUS,banner,{NAME}}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={EDIT_URL*}
	1_TITLE={!EDIT}
	1_ACCESSKEY=q
	1_REL=edit
	1_ICON=admin/edit_this
	{+START,IF,{$ADDON_INSTALLED,tickets}}
		2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=banner:content_id={NAME}:redirect={$SELF_URL&}}
		2_TITLE={!report_content:REPORT_THIS}
		2_ICON=buttons/report
		2_REL=report
	{+END}
{+END}
