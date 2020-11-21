<div class="float_surrounder">
	<div class="cns_profile_column">
		{+START,IF_NON_EMPTY,{AVATAR_URL}}
			<div class="cns_member_profile_avatar">
				<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />
			</div>
		{+END}

		<h2>{!USERGROUPS}</h2>

		<ul class="compact_list">
			<li><span class="role">{PRIMARY_GROUP}</span>{+START,IF_PASSED,ON_PROBATION} <span class="red_alert"><sup>*</sup></span>{+END}</li>
			{+START,LOOP,SECONDARY_GROUPS}
				<li><a href="{$PAGE_LINK*,_SEARCH:groups:view:{_loop_key}}">{_loop_var*}</a>{+START,IF_PASSED,ON_PROBATION} <span class="red_alert"><sup>*</sup></span>{+END}</li>
			{+END}
		</ul>

		<div class="cns_account_links">
			{+START,IF,{VIEW_PROFILES}}
				{+START,LOOP,CUSTOM_FIELDS}
					{$SET,is_messenger_field,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_im_skype_NAME},{!cns_special_cpf:DEFAULT_CPF_im_jabber_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_twitter_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_facebook_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_google_NAME}}}
					{+START,IF,{$GET,is_messenger_field}}
						{+START,SET,messenger_fields}{+START,IF_NON_EMPTY,{RAW_VALUE}}
							{$GET,messenger_fields}
							{+START,IF,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_im_skype_NAME}}}<li><img alt="" src="{$IMG*,icons/24x24/links/skype}" srcset="{$IMG*,icons/48x48/links/skype} 2x"/> <a title="{!PHONE_THEM_UP} {!LINK_NEW_WINDOW}" href="skype:{RAW_VALUE*}?call">{!PHONE_THEM_UP}</a> (Skype)</li>{+END}
							{+START,IF,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_im_jabber_NAME}}}<li><img alt="" src="{$IMG*,icons/24x24/links/xmpp}" srcset="{$IMG*,icons/48x48/links/xmpp} 2x"/> <a title="{!MESSAGE_THEM} {!LINK_NEW_WINDOW}" href="xmpp:{RAW_VALUE*}">{!MESSAGE_THEM}</a> (Jabber/XMPP)</li>{+END}
							{+START,IF,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_sn_twitter_NAME}}}<li><img alt="" src="{$IMG*,icons/24x24/links/twitter}" srcset="{$IMG*,icons/48x48/links/twitter} 2x"/> <a title="{!MESSAGE_THEM} {!LINK_NEW_WINDOW}" href="http://twitter.com/{RAW_VALUE*}" rel="me">@{RAW_VALUE*}</a> (Twitter)</li>{+END}
							{+START,IF,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_sn_facebook_NAME}}}<li><img alt="" src="{$IMG*,icons/24x24/links/facebook}" srcset="{$IMG*,icons/48x48/links/facebook} 2x"/> <a title="{!MESSAGE_THEM} {!LINK_NEW_WINDOW}" href="{RAW_VALUE*}" rel="me">Facebook</a></li>{+END}
							{+START,IF,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_sn_google_NAME}}}<li><img alt="" src="{$IMG*,icons/24x24/links/google_plus}" srcset="{$IMG*,icons/48x48/links/google_plus} 2x"/> <a title="{!MESSAGE_THEM} {!LINK_NEW_WINDOW}" href="{RAW_VALUE*}" rel="me">Google+</a></li>{+END}
						{+END}{+END}
					{+END}
				{+END}
			{+END}
			{+START,IF_NON_EMPTY,{ACTIONS_contact}{$GET,messenger_fields}}
				<div>
					<h2>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!CONTRACT}: {!menus:CONTACT}" title="{!CONTRACT}" src="{$IMG*,1x/trays/contract}" srcset="{$IMG*,2x/trays/contract} 2x" /></a>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{!menus:CONTACT}</a>
					</h2>

					<nav class="toggleable_tray" style="display: block">
						<ul class="nl">
							{ACTIONS_contact}
							{$GET,messenger_fields}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_content}}
				<div>
					<h2>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: {!CONTENT}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{!CONTENT}</a>
					</h2>

					<nav class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
						<ul class="nl">
							{ACTIONS_content}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_views}{ACTIONS_profile}}
				<div>
					<h2>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: {!ACCOUNT}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{!ACCOUNT}</a>
					</h2>

					<nav class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
						<ul class="nl">
							{ACTIONS_views}
							{ACTIONS_profile}
						</ul>
					</nav>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{ACTIONS_audit}}
				<div>
					<h2>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: {!AUDIT}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
						<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{!AUDIT}</a>
					</h2>

					<nav class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
						<ul class="nl">
							{ACTIONS_audit}
						</ul>
					</nav>
				</div>
			{+END}
		</div>

		{$REVIEW_STATUS,member,{MEMBER_ID}}
	</div>

	<div class="cns_profile_main">
		{+START,IF,{$NOT,{VIEW_PROFILES}}}
			<p class="red_alert" role="alert">
				{!ACCESS_DENIED}
			</p>
		{+END}

		{+START,SET,cpf_display}
			{+START,IF,{VIEW_PROFILES}}
				{+START,LOOP,CUSTOM_FIELDS}
					{$SET,is_point_field,{$EQ,{NAME},{!SPECIAL_CPF__cms_points_used},{!SPECIAL_CPF__cms_gift_points_used},{!SPECIAL_CPF__cms_points_gained_chat},{!SPECIAL_CPF__cms_points_gained_given},{!SPECIAL_CPF__cms_points_gained_visiting},{!SPECIAL_CPF__cms_points_gained_rating},{!SPECIAL_CPF__cms_points_gained_voting},{!SPECIAL_CPF__cms_points_gained_wiki}}}
					{$SET,is_messenger_field,{$EQ,{NAME},{!cns_special_cpf:DEFAULT_CPF_im_skype_NAME},{!cns_special_cpf:DEFAULT_CPF_im_jabber_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_twitter_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_facebook_NAME},{!cns_special_cpf:DEFAULT_CPF_sn_google_NAME}}}

					{+START,IF,{$NOR,{$GET,is_point_field},{$GET,is_messenger_field}}}
						<tr id="cpf_{NAME|*}" class="cpf_{FIELD_ID|*}">
							<th class="de_th">
								{NAME*}:
							</th>

							<td>
								<span>
									{+START,IF_EMPTY,{ENCRYPTED_VALUE}}
										{+START,IF_PASSED,EDITABILITY}
											{$SET,edit_type,{EDIT_TYPE}}
											{+START,FRACTIONAL_EDITABLE,{RAW_VALUE},field_{FIELD_ID},_SEARCH:members:view:{MEMBER_ID}:only_tab=edit:only_subtab=settings,{EDITABILITY}}{$SMART_LINK_STRIP,{VALUE},{MEMBER_ID}}{+END}
										{+END}
										{+START,IF_NON_PASSED,EDITABILITY}
											{$SMART_LINK_STRIP,{VALUE},{MEMBER_ID}}
										{+END}
									{+END}
									{+START,IF_NON_EMPTY,{ENCRYPTED_VALUE}}
										{+START,IF,{$JS_ON}}{!encryption:DATA_ENCRYPTED} <a href="javascript:decrypt_data('{ENCRYPTED_VALUE;^*}');" title="{!encryption:DECRYPT_DATA}: {$STRIP_TAGS,{!encryption:DESCRIPTION_DECRYPT_DATA}}">{!encryption:DECRYPT_DATA}</a>{+END}
										{+START,IF,{$NOT,{$JS_ON}}}{ENCRYPTED_VALUE*}{+END}
									{+END}
									<!-- {$,Break out of non-terminated comments in CPF} -->
								</span>
							</td>
						</tr>
					{+END}
				{+END}
			{+END}
		{+END}

		{+START,IF,{$OR,{$IS_NON_EMPTY,{$TRIM,{$GET,cpf_display}}},{$IS_NON_EMPTY,{$TRIM,{SIGNATURE}}}}}
			<h2>{!ABOUT}</h2>

			<div class="wide_table_wrap">
				<table class="map_table wide_table cns_profile_fields cns_profile_about_section">
					{+START,IF,{$NOT,{$MOBILE}}}
						<colgroup>
							<col class="cns_profile_about_field_name_column" />
							<col class="cns_profile_about_field_value_column" />
						</colgroup>
					{+END}

					<tbody>
						{$GET,cpf_display}

						{+START,IF,{$IS_NON_EMPTY,{$TRIM,{SIGNATURE}}}}
							<tr>
								<th class="de_th">
									{!SIGNATURE}:
								</th>

								<td>
									{$SMART_LINK_STRIP,{SIGNATURE},{MEMBER_ID}}
								</td>
							</tr>
						{+END}
					</tbody>
				</table>
			</div>
		{+END}

		{+START,IF,{VIEW_PROFILES}}
			{+START,IF_PASSED,CUSTOM_FIELDS_SECTIONS}
				{+START,LOOP,CUSTOM_FIELDS_SECTIONS}
					<h2>{_loop_key*}</h2>

					<div class="wide_table_wrap">
						<table class="map_table wide_table cns_profile_fields cns_profile_about_section">
							{+START,IF,{$NOT,{$MOBILE}}}
								<colgroup>
									<col class="cns_profile_about_field_name_column" />
									<col class="cns_profile_about_field_value_column" />
								</colgroup>
							{+END}

							<tbody>
								{+START,LOOP,CUSTOM_FIELDS_SECTION}
									<tr id="cpf_{NAME|*}">
										<th class="de_th">
											{NAME*}:
										</th>

										<td>
											<span>
												{+START,IF_EMPTY,{ENCRYPTED_VALUE}}
													{+START,IF,{$EQ,{!ADDRESS}: {NAME},{!cns_special_cpf:SPECIAL_CPF__cms_country}}}
														{$COUNTRY_CODE_TO_NAME,{RAW_VALUE}}
													{+END}

													{+START,IF,{$NEQ,{!ADDRESS}: {NAME},{!cns_special_cpf:SPECIAL_CPF__cms_country}}}
														{+START,IF_PASSED,EDITABILITY}
															{$SET,edit_type,{EDIT_TYPE}}
															{+START,FRACTIONAL_EDITABLE,{RAW_VALUE},field_{FIELD_ID},_SEARCH:members:view:{MEMBER_ID}:only_tab=edit:only_subtab=settings,{EDITABILITY}}{$SMART_LINK_STRIP,{VALUE},{MEMBER_ID}}{+END}
														{+END}
														{+START,IF_NON_PASSED,EDITABILITY}
															{$SMART_LINK_STRIP,{VALUE},{MEMBER_ID}}
														{+END}
													{+END}
												{+END}
												{+START,IF_NON_EMPTY,{ENCRYPTED_VALUE}}
													{+START,IF,{$JS_ON}}{!encryption:DATA_ENCRYPTED} <a href="javascript:decrypt_data('{ENCRYPTED_VALUE;^*}');" title="{!encryption:DECRYPT_DATA}: {$STRIP_TAGS,{!encryption:DESCRIPTION_DECRYPT_DATA}}">{!encryption:DECRYPT_DATA}</a>{+END}
													{+START,IF,{$NOT,{$JS_ON}}}{ENCRYPTED_VALUE*}{+END}
												{+END}
												<!-- {$,Break out of non-terminated comments in CPF} -->
											</span>
										</td>
									</tr>
								{+END}
							</tbody>
						</table>
					</div>
				{+END}
			{+END}
		{+END}

		{+START,IF,{VIEW_PROFILES}}
			<h2>{!DETAILS}</h2>

			<meta class="fn given-name" itemprop="name" content="{$DISPLAYED_USERNAME*,{USERNAME}}" />

			<div class="wide_table_wrap">
				<table class="map_table wide_table cns_profile_details cns_profile_about_section">
					{+START,IF,{$NOT,{$MOBILE}}}
						<colgroup>
							<col class="cns_profile_about_field_name_column" />
							<col class="cns_profile_about_field_value_column" />
						</colgroup>
					{+END}

					<tbody>
						{+START,IF_NON_EMPTY,{$CONFIG_OPTION,display_name_generator}}
							<tr>
								<th class="de_th">{!USERNAME}:</th>
								<td>{USERNAME*}</td>
							</tr>
						{+END}

						<tr>
							<th class="de_th">{!ONLINE_NOW}:</th>
							<td>{ONLINE_NOW*} <span class="associated_details">({$DATE_AND_TIME*,1,0,0,{LAST_VISIT_TIME_RAW}})</span></td>
						</tr>

						{+START,IF_NON_EMPTY,{JOIN_DATE}}
							<tr>
								<th class="de_th">{!JOIN_DATE}:</th>
								<td>
									<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{JOIN_DATE_RAW}}" itemprop="datePublished">{JOIN_DATE*}</time>
								</td>
							</tr>
						{+END}

						{+START,IF_PASSED,ON_PROBATION}{+START,IF_PASSED,PROBATION_GROUP}
							<tr>
								<th class="de_th"><span class="red_alert">{!PROBATION} <sup>*</sup></span>:</th>
								<td>{!_ON_PROBATION_UNTIL,{PROBATION_GROUP*},{USERNAME*},{$DATE_AND_TIME*,1,0,0,{ON_PROBATION}}}</td>
							</tr>
						{+END}{+END}

						<tr>
							<th class="de_th">{!TIME_FOR_THEM}:</th>
							<td>{TIME_FOR_THEM*}</td>
						</tr>

						<tr>
							<th class="de_th">{!TIMEZONE}:</th>
							<td>{USERS_TIMEZONE*}</td>
						</tr>

						{+START,IF_NON_EMPTY,{BANNED}}
							<tr>
								<th class="de_th">{!BANNED}:</th>
								<td>{BANNED*}</td>
							</tr>
						{+END}

						{+START,IF_NON_EMPTY,{DOB}}
							<tr>
								<th class="de_th">{!DATE_OF_BIRTH}:</th>
								<td><span class="bday">{DOB*}</span></td>
							</tr>
						{+END}

						{+START,IF,{$HAS_PRIVILEGE,member_maintenance}}{+START,IF_NON_EMPTY,{EMAIL_ADDRESS}}
							<tr>
								<th class="de_th">{!EMAIL_ADDRESS}:</th>
								<td><a class="email" href="mailto:{EMAIL_ADDRESS*}">{EMAIL_ADDRESS*}</a></td>
							</tr>
						{+END}{+END}

						{+START,LOOP,EXTRA_INFO_DETAILS}
							<tr>
								<th class="de_th">{_loop_key*}:</th>
								<td><span>{_loop_var*}</span></td>
							</tr>
						{+END}
					</tbody>
				</table>
			</div>

			{+START,IF_NON_EMPTY,{PHOTO_URL}}
				<h2>{!PHOTO}</h2>

				<div class="cns_member_profile_photo">
					<a rel="lightbox" href="{$ENSURE_PROTOCOL_SUITABILITY*,{PHOTO_URL}}"><img src="{PHOTO_THUMB_URL*}" alt="{!PHOTO}" class="photo" itemprop="primaryImageOfPage" /></a>
				</div>
			{+END}
		{+END}

		{+START,LOOP,EXTRA_SECTIONS}
			{_loop_var}
		{+END}

		{+START,IF,{VIEW_PROFILES}}
		<div class="stats_overwrap">
			<h2>{!TRACKING}</h2>

			<div class="wide_table_wrap">
				<table class="map_table wide_table cns_profile_tracking cns_profile_about_section">
					{+START,IF,{$NOT,{$MOBILE}}}
						<colgroup>
							<col class="cns_profile_about_field_name_column" />
							<col class="cns_profile_about_field_value_column" />
						</colgroup>
					{+END}

					<tbody>
						{+START,IF,{$ADDON_INSTALLED,cns_forum}}
							<tr>
								<th class="de_th">{!COUNT_POSTS}:</th>
								<td>{COUNT_POSTS*}</td>
							</tr>
						{+END}

						{+START,IF_NON_EMPTY,{MOST_ACTIVE_FORUM}}
							<tr>
								<th class="de_th">{!MOST_ACTIVE_FORUM}:</th>
								<td>{MOST_ACTIVE_FORUM*}</td>
							</tr>
						{+END}

						<tr>
							<th class="de_th">{!LAST_SUBMIT_TIME}:</th>
							<td>{!DAYS_AGO,{SUBMIT_DAYS_AGO}}</td>
						</tr>

						{+START,IF,{$ADDON_INSTALLED,securitylogging}}
							{+START,IF_NON_EMPTY,{IP_ADDRESS}}
								<tr>
									<th class="de_th">{!IP_ADDRESS}:</th>
									<td>
										{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_lookup}}
											<a href="{$PAGE_LINK*,_SEARCH:admin_lookup:param={IP_ADDRESS&}}">{$TRUNCATE_SPREAD,{IP_ADDRESS*},40,1,1}</a>
										{+END}
										{+START,IF,{$NOT,{$HAS_ACTUAL_PAGE_ACCESS,admin_lookup}}}
											{$TRUNCATE_SPREAD,{IP_ADDRESS*},40,1,1}
										{+END}
									</td>
								</tr>
							{+END}
						{+END}

						{+START,IF_PASSED,USER_AGENT}
							<tr>
								<th class="de_th"><abbr title="{!USER_AGENT}">{$PREG_REPLACE*, \([^\(\)]*\),,{!USER_AGENT}}</abbr>:</th>
								<td><abbr title="{USER_AGENT*}">{$PREG_REPLACE*, \([^\(\)]*\),,{$PREG_REPLACE,\.\d+,,{$REPLACE,({OPERATING_SYSTEM}),,{USER_AGENT}}}}</abbr></td>
							</tr>
						{+END}

						{+START,IF_PASSED,OPERATING_SYSTEM}
							<tr>
								<th class="de_th">{!USER_OS}:</th>
								<td>{OPERATING_SYSTEM*}</td>
							</tr>
						{+END}

						{+START,LOOP,EXTRA_TRACKING_DETAILS}
							<tr>
								<th class="de_th">{_loop_key*}:</th>
								<td><span>{_loop_var*}</span></td>
							</tr>
						{+END}
					</tbody>
				</table>
			</div>
		</div>
		{+END}
	</div>
</div>
