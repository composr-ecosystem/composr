{+START,IF_PASSED_AND_TRUE,GET}
	<a {+START,IF_PASSED_AND_TRUE,CONFIRM} data-cms-confirm-click="{!Q_SURE}"{+END} class="link-exempt vertical-alignment" href="{URL*}"{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} title="{ACTION_TITLE*}: {NAME*} {!LINK_NEW_WINDOW}"{+END}{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} target="_blank"{+END}>{+START,INCLUDE,ICON}
		NAME={ICON}
		ICON_SIZE=18
	{+END}</a>
{+END}
{+START,IF_NON_PASSED_OR_FALSE,GET}
	<form class="inline" action="{URL*}" method="post" title="{ACTION_TITLE*}: {NAME*}{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} {!LINK_NEW_WINDOW}{+END}"{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} target="_blank"{+END}>
		<button class="btn-flat-image vertical-alignment" title="{ACTION_TITLE*}: {NAME*}" type="submit">
			{+START,INCLUDE,ICON}
				NAME={ICON}
				ICON_SIZE=18
			{+END}
		</button>
		{+START,IF_PASSED,HIDDEN}{$INSERT_FORM_POST_SECURITY}{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}{+END}
	</form>
{+END}
