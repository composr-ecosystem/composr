{$SET,supports_schema_ratings_and_reviews,1}
<div class="vcalendar vevent" itemscope="itemscope" itemtype="http://schema.org/Event">
	{TITLE}

	<div class="meta-details" role="note">
		<ul class="meta-details-list">
			<li>
				{!BY_SIMPLE,<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author" class="organizer">{$USERNAME*,{SUBMITTER},1}}</a>
				{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
			</li>
			<li>{!ADDED_SIMPLE,<time class="dtstamp" datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{ADD_DATE*}</time>}</li>
			{+START,IF,{$INLINE_STATS}}<li>{!VIEWS_SIMPLE,{VIEWS*}}</li>{+END}
		</ul>
	</div>

	{WARNING_DETAILS}

	<div class="clearfix">
		<div class="box box---calendar-event-screen-description"><div class="box-inner">
			<h2>{!DESCRIPTION}</h2>

			<div class="clearfix">
				{+START,IF_NON_EMPTY,{LOGO}}
					<img class="event-type-image" width="24" height="24" src="{$IMG*,{LOGO}}" alt="{TYPE*}" title="{TYPE*}" />
				{+END}
				{+START,IF_NON_EMPTY,{CONTENT}}
					<div class="description" itemprop="description">{CONTENT}</div>
				{+END}
				{+START,IF_EMPTY,{CONTENT}}
					<div class="no-description">{!NO_DESCRIPTION}</div>
				{+END}
			</div>
		</div></div>
	</div>

	<div class="clearfix">
		{+START,IF_NON_EMPTY,{SUBSCRIBE_URL}}
			<div class="event-right">
				{+START,IF_NON_EMPTY,{SUBSCRIBED}}
					<div class="box box---calendar-event-screen-subscribed"><div class="box-inner">
						<h2>{!SUBSCRIBED_REMINDERS}</h2>

						<div class="accessibility-hidden">{!FOLLOWING_SUBSCRIBED}</div>
						<ul class="nl">
							{+START,LOOP,SUBSCRIBED}
								<li class="attendee"><a class="value" href="{MEMBER_URL*}" itemprop="attendees">{USERNAME*}</a></li>
							{+END}
						</ul>
					</div></div>
				{+END}

				<div class="box box---calendar-event-screen-reminders"><div class="box-inner">
					<h2>{!REMINDERS}</h2>

					{+START,IF_NON_EMPTY,{SUBSCRIPTIONS}}
						<ul class="event-subscriptions">
							{+START,LOOP,SUBSCRIPTIONS}
								<li class="clearfix">
									{TIME*}

									<span class="horiz-field-sep associated-link"><a href="{UNSUBSCRIBE_URL*}" title="{!UNSUBSCRIBE}: {TIME*}">{!UNSUBSCRIBE}</a></span>
								</li>
							{+END}
						</ul>
					{+END}

					<ul class="horizontal-links with-icons associated-links-block-group">
						<li>
							<a href="{SUBSCRIBE_URL*}">{+START,INCLUDE,ICON}
								NAME=buttons/notifications_enable
								ICON_SIZE=24
							{+END} {!SUBSCRIBE_EVENT}</a>
						</li>
					</ul>
				</div></div>
			</div>
		{+END}

		<div{+START,IF_NON_EMPTY,{SUBSCRIBE_URL}} class="event-left"{+END}>
			<table class="map-table wide-table results-table autosized-table" role="note">
				<tbody>
					{+START,IF_NON_EMPTY,{TIME}}
						<tr>
							<th>{!TIME}</th>
							<td>{TIME*}</td>
						</tr>
					{+END}

					{+START,IF_NON_EMPTY,{DAY}}
						<tr>
							<th>{!DATE}</th>
							<td>
								<time class="dtstart" datetime="{TIME_VCAL*}" itemprop="startDate">{DAY*}</time>

								{+START,IF_PASSED,TO_DAY}{+START,IF,{$NEQ,{TO_DAY},{DAY}}}
									&ndash;

									<time class="dtend" datetime="{TO_TIME_VCAL*}" itemprop="endDate">{TO_DAY*}</time>
								{+END}{+END}
							</td>
						</tr>
					{+END}

					{+START,IF_PASSED,TIMEZONE}
						<tr>
							<th>{!TIMEZONE}</th>
							<td>{TIMEZONE*}</td>
						</tr>
					{+END}

					<tr>
						<th>{!TYPE}</th>
						<td class="category">{TYPE*}</td>
					</tr>

					<tr>
						<th>{!PRIORITY}</th>
						<td>{PRIORITY_LANG*}</td>
					</tr>

					{+START,IF_PASSED,MEMBER_CALENDAR}
						<tr>
							<th>{!MEMBER_CALENDAR}</th>
							<td><a href="{$MEMBER_PROFILE_URL*,{MEMBER_CALENDAR}}">{$USERNAME*,{MEMBER_CALENDAR}}</a></td>
						</tr>
					{+END}

					{+START,IF_PASSED,IS_PUBLIC}
						<tr>
							<th>{!IS_PUBLIC}</th>
							<td>{IS_PUBLIC*}</td>
						</tr>
					{+END}

					<tr>
						<th>{!RECURRENCE}</th>
						<td>{RECURRENCE*}</td>
					</tr>

					{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,event,{ID}}}
					{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry},1}{+END}

					{+START,IF_NON_EMPTY,{$REVIEW_STATUS,event,{ID}}}
						<tr>
							<td colspan="2">
								{$REVIEW_STATUS,event,{ID}}
							</td>
						</tr>
					{+END}
				</tbody>
			</table>
		</div>
	</div>

	{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
		<div class="edited" role="note">
			<img alt="" width="9" height="6" src="{$IMG*,edited}" />
			<span>{!EDITED}</span>
			<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}">{$DATE*,,,,{EDIT_DATE_RAW}}</time>
		</div>
	{+END}

	<div itemscope="itemscope" itemtype="http://schema.org/WebPage">
		{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

		{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
		{+START,INCLUDE,STAFF_ACTIONS}
			1_URL={EDIT_URL*}
			1_TITLE={!EDIT}
			1_ACCESSKEY=q
			1_REL=edit
			1_ICON=admin/edit_this
			{$,Do not auto-redirect back to here as recurrences may break so URL hints may no longer be valid}
			1_NOREDIRECT=1

			{+START,IF,{$ADDON_INSTALLED,tickets}}
				2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=event:content_id={ID}:redirect={$SELF_URL&}}
				2_TITLE={!report_content:REPORT_THIS}
				2_ICON=buttons/report
				2_REL=report
			{+END}
		{+END}

		{+START,IF_NON_EMPTY,{RATING_DETAILS}}
			<div class="clearfix">
				<div class="ratings">
					{RATING_DETAILS}
				</div>
			</div>
		{+END}

		<div class="content-screen-comments">
			{COMMENT_DETAILS}
		</div>
	</div>

	{+START,IF_NON_EMPTY,{TRACKBACK_DETAILS}}
		<div class="clearfix">
			<div class="trackbacks">
				{TRACKBACK_DETAILS}
			</div>
		</div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}
</div>
{$SET,supports_schema_reviews,0}
