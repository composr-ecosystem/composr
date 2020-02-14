{$SET,supports_schema_ratings_and_reviews,1}
<div class="vcalendar vevent" itemscope="itemscope" itemtype="http://schema.org/Event">
	{TITLE}

	<div class="meta_details" role="note">
		<ul class="meta_details_list">
			<li>
				{!BY_SIMPLE,<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author" class="organizer">{$USERNAME*,{SUBMITTER},1}}</a>
				{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
			</li>
			<li>{!ADDED_SIMPLE,<time class="dtstamp" datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{ADD_DATE*}</time>}</li>
			{+START,IF,{$INLINE_STATS}}<li>{!VIEWS_SIMPLE,{VIEWS*}}</li>{+END}
		</ul>
	</div>

	{WARNING_DETAILS}

	<div class="float_surrounder">
		{+START,IF_NON_EMPTY,{SUBSCRIBE_URL}}
			<div class="event_right">
				{+START,IF_NON_EMPTY,{SUBSCRIBED}}
					<div class="box box___calendar_event_screen_subscribed"><div class="box_inner">
						<h2>{!SUBSCRIBED_REMINDERS}</h2>

						<div class="accessibility_hidden">{!FOLLOWING_SUBSCRIBED}</div>
						<ul class="nl">
							{+START,LOOP,SUBSCRIBED}
								<li class="attendee"><a class="value" href="{MEMBER_URL*}" itemprop="attendees">{USERNAME*}</a></li>
							{+END}
						</ul>
					</div></div>
				{+END}

				<div class="box box___calendar_event_screen_reminders"><div class="box_inner">
					<h2>{!REMINDERS}</h2>

					{+START,IF_NON_EMPTY,{SUBSCRIPTIONS}}
						<ul class="event_subscriptions">
							{+START,LOOP,SUBSCRIPTIONS}
								<li class="float_surrounder">
									{TIME*}

									<span class="horiz_field_sep associated_link"><a href="{UNSUBSCRIBE_URL*}" title="{!UNSUBSCRIBE}: {TIME*}">{!UNSUBSCRIBE}</a></span>
								</li>
							{+END}
						</ul>
					{+END}

					<ul class="horizontal_links associated_links_block_group">
						<li><img alt="" src="{$IMG*,icons/24x24/buttons/enable_notifications}" srcset="{$IMG*,icons/48x48/buttons/enable_notifications} 2x" /> <a href="{SUBSCRIBE_URL*}">{!SUBSCRIBE_EVENT}</a></li>
					</ul>
				</div></div>
			</div>
		{+END}

		<div{+START,IF_NON_EMPTY,{SUBSCRIBE_URL}} class="event_left"{+END}>
			<div class="box box___calendar_event_screen_description"><div class="box_inner">
				<h2>{!DESCRIPTION}</h2>

				<div class="float_surrounder">
					<img class="event_type_image" src="{$IMG*,{LOGO}}" alt="{TYPE*}" title="{TYPE*}" />
					{+START,IF_NON_EMPTY,{CONTENT}}
						<div class="description" itemprop="description">{CONTENT}</div>
					{+END}
					{+START,IF_EMPTY,{CONTENT}}
						<div class="no_description">{!NO_DESCRIPTION}</div>
					{+END}
				</div>
			</div></div>
		</div>
	</div>

	<div class="float_surrounder">
		<div class="event_right">
			{+START,IF_NON_EMPTY,{TRACKBACK_DETAILS}}
				<div class="trackbacks right">
					{TRACKBACK_DETAILS}
				</div>
			{+END}
			{+START,IF_NON_EMPTY,{RATING_DETAILS}}
				<div class="ratings right">
					{RATING_DETAILS}
				</div>
			{+END}
		</div>

		<div class="event_left">
			<div class="wide_table_wrap"><table class="map_table wide_table results_table autosized_table" role="note">
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
			</table></div>
		</div>
	</div>

	<div itemscope="itemscope" itemtype="http://schema.org/WebPage">
		{+START,IF,{$CONFIG_OPTION,show_content_tagging}}{TAGS}{+END}

		{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
		{+START,INCLUDE,STAFF_ACTIONS}
			1_URL={EDIT_URL*}
			1_TITLE={!EDIT}
			1_ACCESSKEY=q
			1_REL=edit
			1_ICON=menu/_generic_admin/edit_this

			{$,Do not auto-redirect back to here as recurrences may break so URL hints may no longer be valid}
			1_NOREDIRECT=1
		{+END}

		<div class="content_screen_comments">
			{COMMENT_DETAILS}
		</div>
	</div>

	{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
		<div class="edited" role="note">
			<img alt="" src="{$IMG*,1x/edited}" srcset="{$IMG*,2x/edited} 2x" /> {!EDITED}
			<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}">{$DATE*,,,,{EDIT_DATE_RAW}}</time>
		</div>
	{+END}

	{+START,IF,{$CONFIG_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}
</div>
{$SET,supports_schema_reviews,0}
