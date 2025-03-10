{$REQUIRE_JAVASCRIPT,core_rich_media}

<div class="comcode-page-edit-screen" data-tpl="comcodePageEditScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{$PARAGRAPH,{TEXT}}

	{+START,IF_NON_EMPTY,{DELETE_URL}}
		{+START,SET,extra_buttons}
			<input type="hidden" id="delete-field" name="delete" value="0" />
			<button class="btn btn-danger btn-scr js-btn-delete-page" id="delete-button" type="button">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{$?,{IS_TRANSLATION},{!DELETE_TRANSLATION},{!DELETE}}</span></button>
		{+END}
	{+END}

	{POSTING_FORM}

	{REVISIONS}
</div>
