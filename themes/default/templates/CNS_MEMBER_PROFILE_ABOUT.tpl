{$REQUIRE_JAVASCRIPT,core_cns}

<div class="cns-member-profile-about" data-tpl="cnsMemberProfileAbout">
	<div class="cns-profile-column">
		{+START,IF_NON_EMPTY,{AVATAR_URL}}
			<div class="cns-member-profile-avatar">
				<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />
			</div>
		{+END}

		<h2>{!USERGROUPS}</h2>

		<ul class="cns-member-profile-usergroups">
			<li><span class="role">{PRIMARY_GROUP}</span>{+START,IF_PASSED,ON_PROBATION} <span class="red_alert"><sup>*</sup></span>{+END}</li>
			{+START,LOOP,SECONDARY_GROUPS}
				<li><a href="{$PAGE_LINK*,_SEARCH:groups:view:{_loop_key}}">{_loop_var*}</a>{+START,IF_PASSED,ON_PROBATION} <span class="red_alert"><sup>*</sup></span>{+END}</li>
			{+END}
		</ul>

		<div class="cns-account-links">
			{+START,IF,{VIEW_PROFILES}}
				{+START,LOOP,CUSTOM_FIELDS}
					{+START,IF,{$EQ,{SECTION},contact}}
						{+START,IF_NON_EMPTY,{RAW}}
							{+START,SET,messenger_fields}
								{$GET,messenger_fields}
								<li>
									{+START,INCLUDE,CNS_MEMBER_PROFILE_FIELD}{+END}
								</li>
							{+END}
						{+END}
					{+END}
				{+END}
			{+END}
			{+START,IF_NON_EMPTY,{ACTIONS_contact}{$GET,messenger_fields}}
				<div data-toggleable-tray="{}">
					<h2 class="toggleable-tray-unstyled">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
							NAME=trays/contract
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!menus:CONTACT}</a>
					</h2>

					<nav class="toggleable-tray js-tray-content" style="display: block">
						<ul class="cns-account-links-list nl">
							{ACTIONS_contact}
							{$GET,messenger_fields}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_content}}
				<div data-toggleable-tray="{}">
					<h2 class="toggleable-tray-unstyled">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!CONTENT}</a>
					</h2>

					<nav class="toggleable-tray js-tray-content" style="display: none">
						<ul class="cns-account-links-list nl">
							{ACTIONS_content}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_views}{ACTIONS_profile}}
				<div data-toggleable-tray="{}">
					<h2 class="toggleable-tray-unstyled">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!ACCOUNT}</a>
					</h2>

					<nav class="toggleable-tray js-tray-content" style="display: none">
						<ul class="cns-account-links-list nl">
							{ACTIONS_views}
							{ACTIONS_profile}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_audit}}
				<div data-toggleable-tray="{}">
					<h2 class="toggleable-tray-unstyled">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!AUDIT}</a>
					</h2>

					<nav class="toggleable-tray js-tray-content" style="display: none">
						<ul class="cns-account-links-list nl">
							{ACTIONS_audit}
						</ul>
					</nav>
				</div>
			{+END}
		</div>

		{$REVIEW_STATUS,member,{MEMBER_ID}}
	</div>

	<div class="cns-profile-main">
		{+START,IF,{$NOT,{VIEW_PROFILES}}}
			{+START,INCLUDE,RED_ALERT}
				ROLE=alert
				TEXT={!ACCESS_DENIED}
			{+END}
		{+END}

		{+START,SET,cpf_display}
			{+START,IF,{VIEW_PROFILES}}
				{+START,LOOP,CUSTOM_FIELDS}
					{+START,INCLUDE,CNS_MEMBER_PROFILE_FIELDS}{+END}
				{+END}
			{+END}
		{+END}

		{+START,IF,{$OR,{$IS_NON_EMPTY,{$TRIM,{$GET,cpf_display}}},{$IS_NON_EMPTY,{$TRIM,{SIGNATURE}}}}}
			<h2>{!ABOUT}</h2>

			<table class="map-table wide-table cns-profile-fields cns-profile-about-section responsive-blocked-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="cns-profile-about-field-name-column" />
						<col class="cns-profile-about-field-value-column" />
					</colgroup>
				{+END}

				<tbody>
					{$GET,cpf_display}

					{+START,IF,{$IS_NON_EMPTY,{$TRIM,{SIGNATURE}}}}
						<tr>
							<th class="de-th">
								{!SIGNATURE}:
							</th>

							<td>
								{$SMART_LINK_STRIP,{SIGNATURE},{MEMBER_ID}}
							</td>
						</tr>
					{+END}
				</tbody>
			</table>
		{+END}

		{+START,IF,{VIEW_PROFILES}}
			{+START,IF_PASSED,CUSTOM_FIELDS_SECTIONS}
				{+START,LOOP,CUSTOM_FIELDS_SECTIONS}
					{+START,SET,_CUSTOM_FIELDS_SECTION}
						{+START,LOOP,CUSTOM_FIELDS_SECTION}
							{+START,INCLUDE,CNS_MEMBER_PROFILE_FIELDS}{+END}
						{+END}
					{+END}

					{+START,IF_NON_EMPTY,{$TRIM,{$GET,_CUSTOM_FIELDS_SECTION}}}
						<h2>{_loop_key*}</h2>

						<table class="map-table wide-table cns-profile-fields cns-profile-about-section responsive-blocked-table">
							{+START,IF,{$DESKTOP}}
								<colgroup>
									<col class="cns-profile-about-field-name-column" />
									<col class="cns-profile-about-field-value-column" />
								</colgroup>
							{+END}

							<tbody>
								{$GET,_CUSTOM_FIELDS_SECTION}
							</tbody>
						</table>
					{+END}
				{+END}
			{+END}
		{+END}

		{+START,IF,{VIEW_PROFILES}}
			<h2>{!DETAILS}</h2>

			<meta class="fn given-name" itemprop="name" content="{$DISPLAYED_USERNAME*,{USERNAME}}" />

			<table class="map-table wide-table cns-profile-details cns-profile-about-section responsive-blocked-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="cns-profile-about-field-name-column" />
						<col class="cns-profile-about-field-value-column" />
					</colgroup>
				{+END}

				<tbody>
					{+START,IF_NON_EMPTY,{$CONFIG_OPTION,display_name_generator}}
						<tr>
							<th class="de-th">{!USERNAME}:</th>
							<td>{USERNAME*}</td>
						</tr>
					{+END}

					<tr>
						<th class="de-th">{!ONLINE_NOW}:</th>
						<td>{ONLINE_NOW*} <span class="associated-details">({$DATE_TIME*,{LAST_VISIT_TIME_RAW}})</span></td>
					</tr>

					{+START,IF_NON_EMPTY,{JOIN_DATE}}
						<tr>
							<th class="de-th">{!JOIN_DATE}:</th>
							<td>
								<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{JOIN_DATE_RAW}}" itemprop="datePublished">{JOIN_DATE*}</time>
							</td>
						</tr>
					{+END}

					{+START,IF_PASSED,ON_PROBATION}{+START,IF_PASSED,PROBATION_GROUP}
						<tr>
							<th class="de-th">{!PROBATION_EXPIRATION_TIME}</td>
							<td>{!_PROBATION_EXPIRATION_TIME,{PROBATION_GROUP*},{USERNAME*},{$DATE_TIME*,{ON_PROBATION},1}}</td>
						</tr>
					{+END}{+END}

					<tr>
						<th class="de-th">{!TIME_FOR_THEM}:</th>
						<td>{TIME_FOR_THEM*}</td>
					</tr>

					<tr>
						<th class="de-th">{!TIMEZONE}:</th>
						<td>{USERS_TIMEZONE*}</td>
					</tr>

					{+START,IF_NON_EMPTY,{BANNED}}
						<tr>
							<th class="de-th">{!BANNED}:</th>
							<td>{BANNED*}</td>
						</tr>
					{+END}

					{+START,IF_NON_EMPTY,{DOB}}
						<tr>
							<th class="de-th">{DOB_LABEL*}:</th>
							<td><span class="bday">{DOB*}</span></td>
						</tr>
					{+END}

					{+START,IF,{$HAS_PRIVILEGE,view_any_profile_field}}{+START,IF_NON_EMPTY,{EMAIL_ADDRESS}}
						<tr>
							<th class="de-th">{!EMAIL_ADDRESS}:</th>
							<td><a class="email" href="mailto:{EMAIL_ADDRESS*}">{EMAIL_ADDRESS*}</a></td>
						</tr>
					{+END}{+END}

					{+START,LOOP,EXTRA_INFO_DETAILS}
						<tr>
							<th class="de-th">{_loop_key*}:</th>
							<td><span>{_loop_var*}</span></td>
						</tr>
					{+END}
				</tbody>
			</table>

			{+START,IF_NON_EMPTY,{PHOTO_URL}}
				<h2>{!PHOTO}</h2>

				<div class="cns-member-profile-photo">
					<a rel="lightbox" href="{$ENSURE_PROTOCOL_SUITABILITY*,{PHOTO_URL}}"><img src="{$THUMBNAIL*,{PHOTO_URL}}" alt="{!PHOTO}" class="photo" itemprop="primaryImageOfPage" /></a>
				</div>
			{+END}
		{+END}

		{+START,LOOP,EXTRA_SECTIONS}
			{_loop_var}
		{+END}

		{+START,IF,{VIEW_PROFILES}}
			<div class="stats-overwrap">
				<h2>{!TRACKING}</h2>

				<table class="map-table wide-table cns-profile-tracking cns-profile-about-section responsive-blocked-table">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="cns-profile-about-field-name-column" />
							<col class="cns-profile-about-field-value-column" />
						</colgroup>
					{+END}

					<tbody>
						{+START,IF,{$ADDON_INSTALLED,cns_forum}}
							<tr>
								<th class="de-th">{!COUNT_POSTS}:</th>
								<td>{COUNT_POSTS*}</td>
							</tr>
						{+END}

						{+START,IF_NON_EMPTY,{MOST_ACTIVE_FORUM}}
							<tr>
								<th class="de-th">{!MOST_ACTIVE_FORUM}:</th>
								<td>{MOST_ACTIVE_FORUM*}</td>
							</tr>
						{+END}

						{+START,IF_PASSED,SUBMIT_DAYS_AGO}
							<tr>
								<th class="de-th">{!LAST_SUBMIT_TIME}:</th>
								<td>{!_AGO,{!DAYS,{SUBMIT_DAYS_AGO}}}</td>
							</tr>
						{+END}

						{+START,IF,{$ADDON_INSTALLED,securitylogging}}
						{+START,IF_PASSED,IP_ADDRESS}{+START,IF_NON_EMPTY,{IP_ADDRESS}}
								<tr>
									<th class="de-th">{!IP_ADDRESS}:</th>
									<td>
										{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_lookup}}
											<a href="{$PAGE_LINK*,_SEARCH:admin_lookup:results:param={IP_ADDRESS&}}">{$TRUNCATE_SPREAD,{IP_ADDRESS*},40,1,1}</a>
										{+END}
										{+START,IF,{$NOT,{$HAS_ACTUAL_PAGE_ACCESS,admin_lookup}}}
											{$TRUNCATE_SPREAD,{IP_ADDRESS*},40,1,1}
										{+END}
									</td>
								</tr>
							{+END}{+END}
						{+END}

						{+START,IF_PASSED,USER_AGENT}
							<tr>
								<th class="de-th"><abbr title="{!USER_AGENT}">{$PREG_REPLACE*, \([^\(\)]*\),,{!USER_AGENT}}</abbr>:</th>
								<td><abbr title="{USER_AGENT*}">{$PREG_REPLACE*, \([^\(\)]*\),,{$PREG_REPLACE,\.\d+,,{$REPLACE,({OPERATING_SYSTEM}),,{USER_AGENT}}}}</abbr></td>
							</tr>
						{+END}

						{+START,IF_PASSED,OPERATING_SYSTEM}
							<tr>
								<th class="de-th">{!USER_OS}:</th>
								<td>{OPERATING_SYSTEM*}</td>
							</tr>
						{+END}

						{+START,LOOP,EXTRA_TRACKING_DETAILS}
							<tr>
								<th class="de-th">{_loop_key*}:</th>
								<td><span>{_loop_var*}</span></td>
							</tr>
						{+END}
					</tbody>
				</table>
			</div>
		{+END}

		{+START,INCLUDE,STAFF_ACTIONS}
			{+START,IF,{$ADDON_INSTALLED,tickets}}
				1_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=member:content_id={MEMBER_ID}:redirect={$SELF_URL&}}
				1_TITLE={!report_content:REPORT_THIS}
				1_ICON=buttons/report
				1_REL=report
			{+END}
		{+END}
	</div>
</div>
