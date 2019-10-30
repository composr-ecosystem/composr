{TITLE}

<div class="clearfix">
	<div class="calendar-top-navigation">
		<div class="calendar-date-span-link {+START,IF_EMPTY,{YEAR_URL}}current{+END}"><div class="calendar-date-span-link-inner">
			{+START,IF_NON_EMPTY,{YEAR_URL}}
				<a href="{YEAR_URL*}">{!YEARLY}</a>
			{+END}
			{+START,IF_EMPTY,{YEAR_URL}}
				<span>{!YEARLY}</span>
			{+END}
		</div></div>
		<div class="calendar-date-span-link {+START,IF_EMPTY,{MONTH_URL}}current{+END}"><div class="calendar-date-span-link-inner">
			{+START,IF_NON_EMPTY,{MONTH_URL}}
				<a href="{MONTH_URL*}">{!MONTHLY}</a>
			{+END}
			{+START,IF_EMPTY,{MONTH_URL}}
				<span>{!MONTHLY}</span>
			{+END}
		</div></div>
		<div class="calendar-date-span-link {+START,IF_EMPTY,{WEEK_URL}}current{+END}"><div class="calendar-date-span-link-inner">
			{+START,IF_NON_EMPTY,{WEEK_URL}}
				<a href="{WEEK_URL*}">{!WEEKLY}</a>
			{+END}
			{+START,IF_EMPTY,{WEEK_URL}}
				<span>{!WEEKLY}</span>
			{+END}
		</div></div>
		<div class="calendar-date-span-link {+START,IF_EMPTY,{DAY_URL}}current{+END}"><div class="calendar-date-span-link-inner">
			{+START,IF_NON_EMPTY,{DAY_URL}}
				<a href="{DAY_URL*}">{!DAILY}</a>
			{+END}
			{+START,IF_EMPTY,{DAY_URL}}
				<span>{!DAILY}</span>
			{+END}
		</div></div>
	</div>
</div>

<div class="trinav-wrap">
	<div class="trinav-left">
		<a class="btn btn-primary btn-scr buttons--previous" href="{PREVIOUS_URL*}" rel="{+START,IF,{PREVIOUS_NO_FOLLOW}}nofollow {+END}prev" accesskey="j">{+START,INCLUDE,ICON}NAME=buttons/previous{+END} <span>{!PREVIOUS}</span></a>
	</div>
	<div class="trinav-right">
		<a class="btn btn-primary btn-scr buttons--next" href="{NEXT_URL*}" rel="{+START,IF,{NEXT_NO_FOLLOW}}nofollow {+END}next" accesskey="k"><span>{!NEXT}</span> {+START,INCLUDE,ICON}NAME=buttons/next{+END}</a>
	</div>
	<div class="trinav-mid block-desktop">
		{+START,IF_NON_EMPTY,{ADD_URL}}
			<a class="btn btn-primary btn-scr admin--add" rel="add" href="{ADD_URL*}"><span>{+START,INCLUDE,ICON}NAME=admin/add{+END} {!ADD_CALENDAR_EVENT}</span></a>
		{+END}
	</div>
</div>

<div class="horizontal-scrolling">
	{MAIN}
</div>

{+START,IF_NON_EMPTY,{ADD_URL}}
	<p class="buttons-group">
		<span class="buttons-group-inner">
			<a class="btn btn-primary btn-scr admin--add" rel="add" href="{ADD_URL*}"><span>{+START,INCLUDE,ICON}NAME=admin/add{+END} {!ADD_CALENDAR_EVENT}</span></a>
		</span>
	</p>
{+END}

<div class="box box---calendar-main-screen-interests" data-toggleable-tray="{}">
	<div class="box-inner">
		<h2 class="toggleable-tray-title js-tray-header">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">
				{+START,INCLUDE,ICON}
					NAME=trays/expand
					ICON_SIZE=24
				{+END}
			</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!INTERESTS}</a>
		</h2>

		<div class="toggleable-tray js-tray-content" style="display: none" aria-expanded="false">
			<div class="clearfix">
				{+START,IF_NON_EMPTY,{EVENT_TYPES_1}}
					<div class="right event-interest-box"><section class="box"><div class="box-inner">
						<form title="{!INTERESTS}" method="post" action="{INTERESTS_URL*}">
							{$INSERT_SPAMMER_BLACKHOLE}

							<p><strong>{!DESCRIPTION_INTERESTS}</strong></p>

							<div class="calendar-main-page-hidden-data">
								{EVENT_TYPES_1}
							</div>

							<p class="proceed-button">
								<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--choose" type="submit">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} {!INTERESTS}</button>
							</p>
						</form>
					</div></section></div>
				{+END}
				{+START,IF_NON_EMPTY,{EVENT_TYPES_2}}
					<div class="left event-interest-box"><section class="box"><div class="box-inner">
						<form title="{!FILTER}" action="{$URL_FOR_GET_FORM*,{FILTER_URL}}" method="get">
							{$INSERT_SPAMMER_BLACKHOLE}

							{$HIDDENS_FOR_GET_FORM,{FILTER_URL}}

							<p><strong>{!DESCRIPTION_INTERESTS_2}</strong></p>

							<div class="calendar-main-page-hidden-data">
								{EVENT_TYPES_2}
							</div>

							<p class="proceed-button">
								<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
							</p>
						</form>
					</div></section></div>
				{+END}
			</div>
		</div>
	</div>
</div>

{$, Commented out... bloat TODO #3846
{+START,IF,{$ADDON_INSTALLED,syndication_blocks}}
	<div class="box box---calendar-main-screen-feeds-to-overlay" data-toggleable-tray="{}">
		<div class="box-inner">
			<h2 class="toggleable-tray-title js-tray-header">
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">
					{+START,INCLUDE,ICON}
						NAME=trays/expand
						ICON_SIZE=24
					{+END}
				</a>
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!FEEDS_TO_OVERLAY}</a>
			</h2>

			<div class="toggleable-tray js-tray-content" style="display: none" aria-expanded="false">
				{RSS_FORM}
			</div>
		</div>
	</div>
{+END}
}
