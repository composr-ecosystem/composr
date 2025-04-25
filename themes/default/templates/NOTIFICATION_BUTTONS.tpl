{$REQUIRE_JAVASCRIPT,core_notifications}

{+START,IF_PASSED,NOTIFICATIONS_TYPE}
	{$SET,NOTIFICATIONS_TYPE,{NOTIFICATIONS_TYPE}}
{+END}
{+START,IF_NON_PASSED,NOTIFICATIONS_TYPE}
	{$SET,NOTIFICATIONS_TYPE,{$PAGE}}
{+END}

{+START,IF_PASSED,NOTIFICATIONS_PAGE_LINK}
	{$SET,NOTIFICATIONS_PAGE_LINK,{NOTIFICATIONS_PAGE_LINK}}
{+END}
{+START,IF_NON_PASSED,NOTIFICATIONS_PAGE_LINK}
	{$SET,NOTIFICATIONS_PAGE_LINK,_SEARCH:notifications:advanced:{NOTIFICATIONS_ID}:notification_code={$GET,NOTIFICATIONS_TYPE}}
{+END}

{+START,IF_PASSED,BUTTON_TYPE}
	{$SET,button_type,{BUTTON_TYPE}}
{+END}
{+START,IF_NON_PASSED,BUTTON_TYPE}
	{$SET,button_type,btn-scr}
{+END}

{+START,IF_PASSED,BUTTON_LABEL_ENABLE}
	{$SET,button_label_enable,{BUTTON_LABEL_ENABLE}}
{+END}
{+START,IF_NON_PASSED,BUTTON_LABEL_ENABLE}
	{$SET,button_label_enable,{!MONITOR}}
{+END}

{+START,IF_PASSED,BUTTON_LABEL_DISABLE}
	{$SET,button_label_disable,{BUTTON_LABEL_DISABLE}}
{+END}
{+START,IF_NON_PASSED,BUTTON_LABEL_DISABLE}
	{$SET,button_label_disable,{!UNMONITOR}}
{+END}

{+START,IF,{$NOT,{$IS_GUEST}}}{+START,IF,{$NOTIFICATIONS_AVAILABLE,{$GET,NOTIFICATIONS_TYPE}}}
	{$INC,notification_id}
	<div class="inline" data-view="NotificationButtons" data-view-params="{+START,PARAMS_JSON,notification_id}{_*}{+END}">
		{+START,IF_PASSED_AND_TRUE,RIGHT}<div class="clearfix"><div class="right force-margin">{+END}

		<form id="nenable-{$GET*,notification_id}" title="{!notifications:NOTIFICATIONS}"{+START,IF,{$NOTIFICATIONS_ENABLED,{NOTIFICATIONS_ID},{$GET,NOTIFICATIONS_TYPE}}} style="display: none" aria-hidden="true"{+END} data-open-as-overlay="{}" class="inline" rel="enable-notifications" method="post" action="{$PAGE_LINK*,{$GET,NOTIFICATIONS_PAGE_LINK}:redirect={$SELF_URL&*,1,0,0,wide_high=<null>}}">
			{$INSERT_FORM_POST_SECURITY,1}
			<button type="submit" class="btn btn-primary {$GET*,button_type} buttons--notifications-enable js-show-disable-form">{+START,INCLUDE,ICON}NAME=buttons/notifications_enable{+END} {$GET,button_label_enable}</button>
		</form>
		<form id="ndisable-{$GET*,notification_id}" title="{!notifications:NOTIFICATIONS}"{+START,IF,{$NOT,{$NOTIFICATIONS_ENABLED,{NOTIFICATIONS_ID},{$GET,NOTIFICATIONS_TYPE}}}} style="display: none" aria-hidden="true"{+END} data-open-as-overlay="{}" class="inline" rel="disable-notifications" method="post" action="{$PAGE_LINK*,{$GET,NOTIFICATIONS_PAGE_LINK}:redirect={$SELF_URL&*,1,0,0,wide_high=<null>}}">
			{$INSERT_FORM_POST_SECURITY,1}
			<button type="submit" class="btn btn-primary {$GET*,button_type} buttons--notifications-disable js-show-enable-form">{+START,INCLUDE,ICON}NAME=buttons/notifications_disable{+END} {$GET,button_label_disable}</button>
		</form>

		{+START,IF_PASSED_AND_TRUE,RIGHT}</div></div>{+END}
	</div>
{+END}{+END}
