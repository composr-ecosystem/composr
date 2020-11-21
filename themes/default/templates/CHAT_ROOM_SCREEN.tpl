{TITLE}

{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,widget_color}
{$REQUIRE_CSS,widget_color}

{+START,IF_NON_EMPTY,{INTRODUCTION}}<p>{INTRODUCTION}</p>{+END}

{CHAT_SOUND}
<script>
// <![CDATA[
	add_event_listener_abstract(window,'load',prepare_chat_sounds);
// ]]>
</script>

<div class="chat_posting_area">
	<div class="float_surrounder">
		<div class="left">
			<form title="{!MESSAGE}" action="{MESSAGES_PHP*}?action=post&amp;room_id={CHATROOM_ID*}" method="post" class="inline" autocomplete="off">
				{$INSERT_SPAMMER_BLACKHOLE}

				<div style="display: inline;">
					<p class="accessibility_hidden"><label for="post">{!MESSAGE}</label></p>
					<textarea style="font-family: {FONT_NAME_DEFAULT;*}" class="input_text_required"{+START,IF,{$NOT,{$MOBILE}}} onkeyup="manage_scroll_height(this);"{+END} onkeypress="if (enter_pressed(event)) return chat_post(event,{CHATROOM_ID*},'post',document.getElementById('font_name').options[document.getElementById('font_name').selectedIndex].value,document.getElementById('text_colour').value); return true;" id="post" name="message" cols="{$?,{$MOBILE},37,39}" rows="1"></textarea>
					<input type="hidden" name="font" id="font" value="{FONT_NAME_DEFAULT*}" />
					<input type="hidden" name="colour" id="colour" value="{TEXT_COLOUR_DEFAULT*}" />
				</div>
			</form>
		</div>
		<div class="left">
			<form title="{SUBMIT_VALUE*}" action="{MESSAGES_PHP*}?action=post&amp;room_id={CHATROOM_ID*}" method="post" class="inline" autocomplete="off">
				{$INSERT_SPAMMER_BLACKHOLE}

				<input type="button" class="button_micro buttons__send" onclick="return chat_post(event,{CHATROOM_ID*},'post',document.getElementById('font_name').options[document.getElementById('font_name').selectedIndex].value,document.getElementById('text_colour').value);" value="{SUBMIT_VALUE*}" />
			</form>
			{+START,IF,{$NOT,{$MOBILE}}}
				{MICRO_BUTTONS}
				{+START,IF,{$CNS}}
					<a rel="nofollow" class="horiz_field_sep" tabindex="6" href="#" onclick="window.faux_open(maintain_theme_in_link('{$FIND_SCRIPT;*,emoticons}?field_name=post{$KEEP;*}'),'emoticon_chooser','width=300,height=320,status=no,resizable=yes,scrollbars=no'); return false;" title="{!EMOTICONS_POPUP}"><img alt="{!EMOTICONS}" src="{$IMG*,icons/16x16/editor/insert_emoticons}" srcset="{$IMG*,icons/32x32/editor/insert_emoticons} 2x" /></a>
				{+END}
			{+END}
		</div>
		<div class="right">
			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray('chat_comcode_panel');"><img id="e_chat_comcode_panel" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" alt="{!CHAT_TOGGLE_COMCODE_BOX}" title="{!CHAT_TOGGLE_COMCODE_BOX}" /></a>
		</div>
	</div>

	<div style="display: {$JS_ON,none,block}" id="chat_comcode_panel">
		{BUTTONS}

		{+START,IF_NON_EMPTY,{COMCODE_HELP}{CHATCODE_HELP}}
			<ul class="horizontal_links horiz_field_sep associated_links_block_group">
				{+START,IF_NON_EMPTY,{COMCODE_HELP}}
					<li><a onclick="return open_link_as_overlay(this);" class="link_exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{COMCODE_HELP*}"><img src="{$IMG*,icons/16x16/editor/comcode}" srcset="{$IMG*,icons/32x32/editor/comcode} 2x" class="vertical_alignment" alt="" /></a></li>
				{+END}
				{+START,IF_NON_EMPTY,{CHATCODE_HELP}}
					<li><a onclick="return open_link_as_overlay(this);" class="link_exempt" title="{$STRIP_TAGS,{!CHATCODE_HELP}} {!LINK_NEW_WINDOW}" target="_blank" href="{CHATCODE_HELP*}">{!CHATCODE_HELP}</a></li>
				{+END}
			</ul>
		{+END}
	</div>
</div>

<div class="messages_window"><div role="marquee" class="messages_window_full_chat" id="messages_window"></div></div>

<div class="box box___chat_screen_chatters"><p class="box_inner">
	{!USERS_IN_CHATROOM} <span id="chat_members_update">{CHATTERS}</span>
</p></div>

<form title="{$STRIP_TAGS,{!CHAT_OPTIONS_DESCRIPTION}}" class="below_main_chat_window" onsubmit="return check_chat_options(this);" method="post" action="{OPTIONS_URL*}" autocomplete="off">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div class="box box___chat_screen_options box_prominent"><div class="box_inner">
		<h2>{!OPTIONS}</h2>

		<div class="chat_room_options">
			<p class="chat_options_title">
				{!CHAT_OPTIONS_DESCRIPTION}
			</p>

			<div class="float_surrounder">
				<div class="chat_colour_option">
					<p>
						<label for="text_colour">{!CHAT_OPTIONS_COLOUR_NAME}:</label>
					</p>
					<p>
						<input size="10" maxlength="7" class="input_line_required" type="color" id="text_colour" name="text_colour" value="{+START,IF,{$NEQ,{TEXT_COLOUR_DEFAULT},inherit}}{TEXT_COLOUR_DEFAULT*}{+END}" onchange="if (this.form.elements['text_colour'].value.match(/^#[0-9A-F][0-9A-F][0-9A-F]([0-9A-F][0-9A-F][0-9A-F])?$/)) { this.style.color=this.value; document.getElementById('colour').value=this.value; update_picker_colour(); }" />
					</p>
				</div>

				<div class="chat_font_option">
					<p>
						<label for="font_name">{!CHAT_OPTIONS_TEXT_NAME}:</label>
					</p>
					<p>
						<select onclick="this.onchange(event);" onchange="on_font_change(this);" id="font_name" name="font_name">
							{+START,LOOP,Arial\,Courier\,Georgia\,Impact\,Times\,Trebuchet\,Verdana\,Tahoma\,Geneva\,Helvetica}
								<option {$?,{$EQ,{FONT_NAME_DEFAULT},{_loop_var}},selected="selected" ,}value="{_loop_var*}" style="font-family: '{_loop_var;*}'">{_loop_var*}</option>
							{+END}
						</select>
					</p>
				</div>
			</div>

			<p>
				<label for="play_sound">{!SOUND_EFFECTS}:</label> <input type="checkbox" id="play_sound" name="play_sound" checked="checked" />
			</p>

			<p>
				<input class="button_screen_item buttons__save" onclick="var form=this.form; window.fauxmodal_confirm('{!SAVE_COMPUTER_USING_COOKIE}',function(answer) { if (answer) form.submit(); }); return false;" type="submit" value="{$STRIP_TAGS,{!CHAT_CHANGE_OPTIONS}}" />
			</p>
		</div>

		<div class="chat_room_actions">
			<p class="lonely_label">{!ACTIONS}:</p>
			<nav>
				<ul class="actions_list">
					{+START,LOOP,LINKS}
						{+START,IF_NON_EMPTY,{_loop_var}}
							<li class="icon_14_{_loop_key*}">{_loop_var}</li>
						{+END}
					{+END}
				</ul>
			</nav>
		</div>
	</div></div>
</form>

<div class="force_margin">
	{+START,INCLUDE,NOTIFICATION_BUTTONS}
		NOTIFICATIONS_TYPE=member_entered_chatroom
		NOTIFICATIONS_ID={CHATROOM_ID}
		BREAK=1
	{+END}
</div>

<script>// <![CDATA[
	add_event_listener_abstract(window,'real_load',function() {
		chat_load({CHATROOM_ID%});
	});
// ]]></script>

{$REVIEW_STATUS,chat,{CHATROOM_ID}}
