{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,widget_color}
{$REQUIRE_JAVASCRIPT,chat}
{$REQUIRE_CSS,widget_color}
{$REQUIRE_CSS,forms}

<div data-view="ChatRoomScreen" data-view-params="{+START,PARAMS_JSON,CHATROOM_ID}{_*}{+END}">
	{TITLE}

	{+START,IF_NON_EMPTY,{INTRODUCTION}}<p>{INTRODUCTION}</p>{+END}

	{CHAT_SOUND}

	{+START,SET,posting_box}
		<div class="chat-posting-area">
			<div class="clearfix">
				<div class="left">
					<form title="{!MESSAGE}" action="{MESSAGES_PHP*}?action=post&amp;room_id={CHATROOM_ID*}" method="post" class="inline">
						{$INSERT_FORM_POST_SECURITY}

						<div style="display: inline;">
							<p class="accessibility-hidden"><label for="post">{!MESSAGE}</label></p>
							<textarea style="font-family: '{FONT_NAME_DEFAULT;*}'" class="form-control input-text-required js-keypress-enter-post-chat" data-textarea-auto-height="" id="post" name="message" cols="37" rows="1"></textarea>
							<input type="hidden" name="font" id="font" value="{FONT_NAME_DEFAULT*}" />
							<input type="hidden" name="colour" id="colour" value="{TEXT_COLOUR_DEFAULT*}" />
						</div>
					</form>
				</div>

				<div class="right">
					<a class="toggleable-tray-button js-btn-toggle-chat-comcode-panel" href="#!" title="{!CHAT_TOGGLE_COMCODE_BOX}">{+START,INCLUDE,ICON}
						ID=e-chat-comcode-panel
						NAME=trays/expand
						ICON_SIZE=20
					{+END}</a>
				</div>

				<div class="left">
					<form title="{SUBMIT_VALUE*}" action="{MESSAGES_PHP*}?action=post&amp;room_id={CHATROOM_ID*}" method="post" class="inline">
						{$INSERT_FORM_POST_SECURITY}

						<button type="button" class="btn btn-primary btn-sm buttons--send js-click-post-chat-message">{+START,INCLUDE,ICON}NAME=buttons/send{+END} {SUBMIT_VALUE*}</button>
					</form>
					{+START,IF,{$DESKTOP}}
						<span class="inline-desktop">
							{MICRO_BUTTONS}
							{+START,IF,{$CNS}}
								<a rel="nofollow" class="horiz-field-sep js-click-open-emoticon-chooser-window" tabindex="6" href="#!" title="{!EMOTICONS_POPUP}">{+START,INCLUDE,ICON}
									NAME=editor/insert_emoticons
									ICON_SIZE=24
								{+END}</a>
							{+END}
						</span>
					{+END}
				</div>
			</div>

			<div style="display: none" id="chat-comcode-panel">
				{BUTTONS}

				{+START,IF_NON_EMPTY,{COMCODE_HELP}{CHATCODE_HELP}}
					<ul class="horizontal-links with-icons horiz-field-sep associated-links-block-group">
						{+START,IF_NON_EMPTY,{COMCODE_HELP}}
							<li><a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{COMCODE_HELP*}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a></li>
						{+END}
						{+START,IF_NON_EMPTY,{CHATCODE_HELP}}
							<li><a data-open-as-overlay="{}" class="link-exempt" title="{$STRIP_TAGS,{!CHATCODE_HELP}} {!LINK_NEW_WINDOW}" target="_blank" href="{CHATCODE_HELP*}">{!CHATCODE_HELP}</a></li>
						{+END}
					</ul>
				{+END}
			</div>
		</div>
	{+END}

	{+START,IF,{$EQ,{$CONFIG_OPTION,chat_message_direction},upwards}}
		{$GET,posting_box}
	{+END}

	<div class="messages-window"><div role="marquee" class="messages-window-full-chat" id="messages-window"></div></div>

	{+START,IF,{$EQ,{$CONFIG_OPTION,chat_message_direction},downwards}}
		{$GET,posting_box}
	{+END}

	<div class="box box---chat-screen-chatters"><p class="box-inner">
		{!USERS_IN_CHATROOM} <span id="chat-members-update">{CHATTERS}</span>
	</p></div>

	<form title="{$STRIP_TAGS,{!CHAT_OPTIONS_DESCRIPTION}}" class="below-main-chat-window js-form-submit-check-chat-options" method="post" action="{OPTIONS_URL*}">
		{$INSERT_FORM_POST_SECURITY}

		<div class="box box---chat-screen-options box-prominent"><div class="box-inner">
			<h2>{!OPTIONS}</h2>

			<div class="chat-room-options">
				<p class="chat-options-title">
					{!CHAT_OPTIONS_DESCRIPTION}
				</p>

				<div class="clearfix">
					<div class="chat-colour-option">
						<p>
							<label for="text_colour">{!CHAT_OPTIONS_COLOUR_NAME}:</label>
						</p>
						<p>
							<input size="10" maxlength="7" class="input-line-required js-change-input-text-color" type="color" id="text_colour" name="text_colour" value="{+START,IF,{$NEQ,{TEXT_COLOUR_DEFAULT},inherit}}{TEXT_COLOUR_DEFAULT*}{+END}" />
						</p>
					</div>

					<div class="chat-font-option">
						<p>
							<label for="font_name">{!CHAT_OPTIONS_TEXT_NAME}:</label>
						</p>
						<p>
							<select class="form-control js-select-click-font-change js-select-change-font-change" id="font_name" name="font_name">
								{+START,LOOP,={$FONTS}}
									{+START,IF_NON_EMPTY,{_loop_var}}
										<option {$?,{$EQ,{FONT_NAME_DEFAULT},{_loop_var}},selected="selected" ,}value="{_loop_var*}" style="font-family: '{_loop_var;*}', sans-serif">{_loop_var*}</option>
									{+END}
								{+END}
							</select>
						</p>
					</div>
				</div>

				<p>
					<label for="play_sound">{!SOUND_EFFECTS}:</label> <input type="checkbox" id="play_sound" name="play_sound" checked="checked" />
				</p>

				<p>
					<button class="btn btn-primary btn-scri buttons--save" data-cms-confirm-click="{!SAVE_COMPUTER_USING_COOKIE}" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {$STRIP_TAGS,{!CHAT_CHANGE_OPTIONS}}</button>
				</p>
			</div>

			<div class="chat-room-actions">
				<p class="lonely-label">{!ACTIONS}:</p>
				<nav>
					<ul class="actions-list">
						{+START,LOOP,LINKS}
							{+START,IF_NON_EMPTY,{_loop_var}}
								<li>{+START,INCLUDE,ICON}NAME={_loop_key}{+END} {_loop_var}</li>
							{+END}
						{+END}
					</ul>
				</nav>
			</div>
		</div></div>
	</form>

	<div class="force-margin">
		{+START,INCLUDE,NOTIFICATION_BUTTONS}
			NOTIFICATIONS_TYPE=member_entered_chatroom
			NOTIFICATIONS_ID={CHATROOM_ID}
			BREAK=1
		{+END}
	</div>

	{+START,INCLUDE,STAFF_ACTIONS}
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			1_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=chat:content_id={CHATROOM_ID}:redirect={$SELF_URL&}}
			1_TITLE={!report_content:REPORT_THIS}
			1_ICON=buttons/report
			1_REL=report
		{+END}
	{+END}

	{$REVIEW_STATUS,chat,{CHATROOM_ID}}
</div>
