<section id="tray_{!MEMBER|}" class="box cns_information_bar_outer">
	<h2 class="toggleable_tray_title">
		<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode,false,'{!MEMBER|}');"><img alt="{!CONTRACT}: {$STRIP_TAGS,{!_LOGIN}}" title="{!CONTRACT}" src="{$IMG*,1x/trays/contract2}" srcset="{$IMG*,2x/trays/contract2} 2x" /></a>

		<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode,false,'{!MEMBER|}');">{!_LOGIN}{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}} / {!SEARCH}{+END}{+END}</a>
	</h2>

	<div class="toggleable_tray">
		<div class="cns_information_bar float_surrounder">
			<div class="cns_guest_column cns_guest_column_a">
				<form title="{!_LOGIN}" onsubmit="if (check_field_for_blankness(this.elements['login_username'],event)) { disable_button_just_clicked(this); return true; } return false;" action="{LOGIN_URL*}" method="post" class="inline" autocomplete="on">
					<div>
						<div class="accessibility_hidden"><label for="member_bar_login_username">{$LOGIN_LABEL}</label></div>
						<div class="accessibility_hidden"><label for="member_bar_s_password">{!PASSWORD}</label></div>
						<input maxlength="80" size="15" type="text" onfocus="placeholder_focus(this);" onblur="placeholder_blur(this);" class="field_input_non_filled" alt="{!USERNAME}" value="{!USERNAME}" id="member_bar_login_username" name="login_username" />
						<input maxlength="255" size="15" type="password" placeholder="{!PASSWORD}" value="" name="password" id="member_bar_s_password" />
						{+START,IF,{$CONFIG_OPTION,password_cookies}}
							<label for="remember">{!REMEMBER_ME}:</label> <input{+START,IF,{$CONFIG_OPTION,remember_me_by_default}} checked="checked"{+END}{+START,IF,{$NOT,{$CONFIG_OPTION,remember_me_by_default}}} onclick="if (this.checked) { var t=this; window.fauxmodal_confirm('{!REMEMBER_ME_COOKIE;}',function(answer) { if (!answer) t.checked=false; });  }"{+END} type="checkbox" value="1" id="remember" name="remember" />
						{+END}
						<input class="button_screen_item menu__site_meta__user_actions__login" type="submit" value="{!_LOGIN}" />

						<ul class="horizontal_links associated_links_block_group horiz_field_sep">
							<li><a href="{JOIN_URL*}">{!_JOIN}</a></li>
							<li><a onclick="return open_link_as_overlay(this);" rel="nofollow" href="{FULL_LOGIN_URL*}" title="{!MORE}: {!_LOGIN}">{!MORE}</a></li>
						</ul>
					</div>
				</form>
			</div>
			{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}}
				<div class="cns_guest_column cns_guest_column_c">
					{+START,INCLUDE,MEMBER_BAR_SEARCH}{+END}
				</div>
			{+END}{+END}

			<nav class="cns_guest_column cns_member_column_d">
				{$,<p class="cns_member_column_title">{!VIEW}:</p>}
				<ul class="actions_list">
					<li><a onclick="return open_link_as_overlay(this);" href="{NEW_POSTS_URL*}">{!POSTS_SINCE}</a></li>
					<li><a onclick="return open_link_as_overlay(this);" href="{UNANSWERED_TOPICS_URL*}">{!UNANSWERED_TOPICS}</a></li>
				</ul>
			</nav>
		</div>
	</div>
</section>

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		{+START,IF,{$JS_ON}}
			handle_tray_cookie_setting('{!MEMBER|}');
		{+END}
	});
//]]></script>

