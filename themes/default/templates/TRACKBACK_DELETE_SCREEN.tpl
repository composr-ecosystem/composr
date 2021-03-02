{TITLE}

{+START,IF_NON_EMPTY,{TRACKBACKS}}
	{+START,IF,{LOTS}}
		<p><em>{!ONLY_1000_SHOWN}</em></p>
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" action="{$PAGE_LINK*,_SEARCH:admin_trackbacks:delete}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		{TRACKBACKS}

		<p class="proceed-button">
			<button data-disable-on-click="1" class="btn btn-danger btn-scr" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!DELETE}</button>
		</p>
	</form>
{+END}
{+START,IF_EMPTY,{TRACKBACKS}}
	<p class="nothing-here">{!NO_ENTRIES}</p>
{+END}
