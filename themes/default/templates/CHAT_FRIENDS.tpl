{$REQUIRE_JAVASCRIPT,chat}
{+START,IF_NON_EMPTY,{FRIENDS}}
	<div data-tpl="chatFriends" data-tpl-params="{+START,PARAMS_JSON,FRIENDS}{_*}{+END}">
		<table class="columned-table results-table wide-table autosized-table">
			<colgroup>
				<col class="chat-friends-column" />

				<col class="chat-name-column" />

				{+START,IF,{$NOT,{SIMPLER}}}
					<col class="chat-online-column" />
				{+END}

				{+START,IF,{$NOT,{SIMPLER}}}
					<col class="chat-choose-column" />
				{+END}
			</colgroup>

			<thead>
				<tr>
					<th></th>

					<th>
						{+START,IF,{$NOT,{SIMPLER}}}
							{!NAME}
						{+END}

						{+START,IF,{SIMPLER}}
							{!FRIEND}
						{+END}
					</th>

					{+START,IF,{$NOT,{SIMPLER}}}
						<th>
							<a target="_blank" title="{!ONLINE} {!LINK_NEW_WINDOW}" href="{ONLINE_URL*}">{!ONLINE}</a>
						</th>
					{+END}

					{+START,IF,{$NOT,{SIMPLER}}}
						<th>
							{!CHOOSE}
						</th>
					{+END}
				</tr>
			</thead>
			<tbody>
				{+START,LOOP,FRIENDS}
					<tr>
						<td>
							<a {+START,IF,{SIMPLER}} title="{ONLINE_TEXT*}"{+END}>{+START,INCLUDE,ICON}
								NAME=menu/social/members
								ICON_ID=friend-img-{MEMBER_ID*}
								ICON_SIZE=24
							{+END}</a>
						</td>

						<td>
							{+START,IF,{SIMPLER}}
								{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={MEMBER_ID}{+END}
							{+END}

							{+START,IF,{CAN_IM}}
								<a rel="friend" title="{$DISPLAYED_USERNAME*,{USERNAME}}: {!START_IM}" href="{$PAGE_LINK;*,_SEARCH:chat:enter_im={MEMBER_ID}}" class="js-click-start-friend-im" data-tp-member-id="{MEMBER_ID*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>
							{+END}
							{+START,IF,{$NOT,{CAN_IM}}}
								{$DISPLAYED_USERNAME*,{USERNAME}}
							{+END}
						</td>

						{+START,IF,{$NOT,{SIMPLER}}}
							<td id="online-{MEMBER_ID*}">
								{ONLINE_TEXT*}
							</td>
						{+END}

						{+START,IF,{$NOT,{SIMPLER}}}
							<td>
								<label class="accessibility-hidden" for="select_{MEMBER_ID*}">{!CHOOSE}</label>
								<input type="checkbox" id="select_{MEMBER_ID*}" value="1" name="select_{MEMBER_ID*}" />
							</td>
						{+END}
					</tr>
				{+END}
			</tbody>
		</table>
	</div>
{+END}

{+START,IF_EMPTY,{FRIENDS}}
	{+START,IF_EMPTY,{FRIENDS_ONLINE}{FRIENDS_OFFLINE}}
		<p class="nothing-here">{!NO_FRIEND_ENTRIES}</p>
	{+END}
	{+START,IF_NON_EMPTY,{FRIENDS_ONLINE}{FRIENDS_OFFLINE}}
		<p class="nothing-here">{!NOBODY_ONLINE}</p>
	{+END}
{+END}
