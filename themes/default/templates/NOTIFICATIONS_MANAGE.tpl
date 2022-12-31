<p>
	{INTRO*}
</p>

<table class="columned-table wide-table results-table notifications-form responsive-table responsive-table-bolded-first-column">
	<colgroup>
		<col class="notifications-field-name-column" />
		{+START,IF_PASSED_AND_TRUE,SHOW_PRIVILEGES}
			<col class="notifications-privileges-column" />
		{+END}
		{+START,LOOP,NOTIFICATION_TYPES_TITLES}
			<col class="notifications-tick-column" />
		{+END}
		{+START,IF,{ADVANCED_COLUMN}}
			<col class="notifications-advanced-column" />
		{+END}
	</colgroup>

	<thead>
		<tr>
			<th></th>
			{+START,IF_PASSED_AND_TRUE,SHOW_PRIVILEGES}
				<th>
					{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{!NOTIFICATION_PRIVILEGED},UL_ESCAPED}{$KEEP}}
					<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{!NOTIFICATION_PRIVILEGED}" alt="{!NOTIFICATION_PRIVILEGED}" />
				</th>
			{+END}
			{+START,LOOP,NOTIFICATION_TYPES_TITLES}
				<th>
					{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{LABEL},UL_ESCAPED}{$KEEP}}
					<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="" alt="{LABEL*}" />
				</th>
			{+END}
			{+START,IF,{ADVANCED_COLUMN}}
				<th></th>
			{+END}
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,NOTIFICATION_SECTIONS}
			<tr class="form-table-field-spacer">
				<th class="js-responsive-table-no-prefix table-heading-cell" colspan="{$SET,num_columns,{$ADD,{NOTIFICATION_TYPES_TITLES},1}}{+START,IF_PASSED_AND_TRUE,SHOW_PRIVILEGES}{$INC,num_columns}{+END}{+START,IF,{ADVANCED_COLUMN}}{$INC,num_columns}{+END}{$GET*,num_columns}">
					<span class="h2">{NOTIFICATION_SECTION*}</span>
				</th>
			</tr>

			{+START,LOOP,NOTIFICATION_CODES}
				<tr class="notification-code {$CYCLE*,zebra,zebra-0,zebra-1}">
					<th class="de-th">{NOTIFICATION_LABEL*}</th>

					{+START,IF_PASSED,PRIVILEGED}
						<td>{$?,{PRIVILEGED},{!YES},{!NO}}</td>
					{+END}

					{NOTIFICATION_TYPES}

					{+START,IF,{ADVANCED_COLUMN}}
						{+START,SET,advanced_link}
							{+START,IF,{SUPPORTS_CATEGORIES}}
								<span class="associated-link"><a data-open-as-overlay="{'target': '_self'}" href="{$PAGE_LINK*,_SEARCH:notifications:advanced:notification_code={NOTIFICATION_CODE}{$?,{$NEQ,{MEMBER_ID},{$MEMBER}},:keep_su={$USERNAME&,{MEMBER_ID}}}}">{+START,IF,{$DESKTOP}}<span class="inline-desktop">{!ADVANCED}</span>{+END}<span class="inline-mobile">{!MORE}</span></a></span>
							{+END}
						{+END}
					{+END}
					<td class="associated-details">{$TRIM,{$GET,advanced_link}}</td>
				</tr>
			{+END}
		{+END}
	</tbody>
</table>

{+START,IF_PASSED,AUTO_NOTIFICATION_CONTRIB_CONTENT}
	<h2>{!cns:AUTO_NOTIFICATION_CONTRIB_CONTENT}</h2>

	<p class="simple-neat-checkbox">
		<input {+START,IF,{AUTO_NOTIFICATION_CONTRIB_CONTENT}} checked="checked"{+END} type="checkbox" id="auto-monitor-contrib-content" name="auto_monitor_contrib_content" value="1" />
		<label for="auto-monitor-contrib-content"><span>{!cns:DESCRIPTION_AUTO_NOTIFICATION_CONTRIB_CONTENT}</span></label>
	</p>
{+END}

{+START,IF_PASSED,SMART_TOPIC_NOTIFICATION}
	<h2>{!cns:SMART_TOPIC_NOTIFICATION}</h2>

	<p class="simple-neat-checkbox">
		<input {+START,IF,{SMART_TOPIC_NOTIFICATION}} checked="checked"{+END} type="checkbox" id="smart-topic-notification" name="smart_topic_notification" value="1" />
		<label for="smart-topic-notification"><span>{!cns:DESCRIPTION_SMART_TOPIC_NOTIFICATION}</span></label>
	</p>
{+END}

{+START,IF_PASSED,MAILING_LIST_STYLE}{+START,IF_PASSED,MAILING_LIST_STYLE_DESCRIPTION}
	<h2>{!cns_mailinglists:MAILING_LIST_STYLE}</h2>

	<p class="simple-neat-checkbox">
		<input {+START,IF,{MAILING_LIST_STYLE}} checked="checked"{+END} type="checkbox" id="mailing-list-style" name="mailing_list_style" value="1" />
		<label for="mailing-list-style"><span>{MAILING_LIST_STYLE_DESCRIPTION}</span></label>
	</p>
{+END}{+END}
