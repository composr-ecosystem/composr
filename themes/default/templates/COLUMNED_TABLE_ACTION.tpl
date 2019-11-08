{+START,IF_PASSED_AND_TRUE,GET}
	<a {+START,IF_PASSED_AND_TRUE,CONFIRM} data-cms-confirm-click="{!Q_SURE}"{+END} class="link-exempt vertical-alignment" href="{URL*}"{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} title="{ACTION_TITLE*}: {NAME*} {!LINK_NEW_WINDOW}"{+END}{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} target="_blank"{+END}>{+START,INCLUDE,ICON}
		NAME={ICON}
		ICON_SIZE=14
	{+END}</a>
{+END}
{+START,IF_NON_PASSED_OR_FALSE,GET}
	<form class="inline top-vertical-alignment" action="{URL*}" method="post" title="{ACTION_TITLE*}: {NAME*}{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} {!LINK_NEW_WINDOW}{+END}"{+START,IF_PASSED_AND_TRUE,NEW_WINDOW} target="_blank"{+END}>
		<button title="{ACTION_TITLE*}: {NAME*}" type="submit">
			{+START,INCLUDE,ICON}
				NAME={ICON}
				ICON_SIZE=14
			{+END}
		</button>
		{+START,IF_PASSED,HIDDEN}{$INSERT_SPAMMER_BLACKHOLE}{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}{+END}
	</form>
{+END}
