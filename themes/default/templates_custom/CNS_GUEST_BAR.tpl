{$REQUIRE_JAVASCRIPT,facebook_support}

<section id="tray-{!MEMBER|}" data-tpl="cnsGuestBar" data-toggleable-tray="{ save: true }" class="box cns-information-bar-outer">
	<div class="box-inner">
		<h2 class="toggleable-tray-title js-tray-header">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray inline-desktop" href="#!" title="{!CONTRACT}">
				{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
				{+END}
			</a>

			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!_LOGIN}{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}} / {!SEARCH}{+END}{+END}{+END}</a>
		</h2>

		<div class="toggleable-tray js-tray-content">
			<div class="cns-information-bar clearfix">
				<div class="cns-guest-column cns-guest-column-a">
					<form title="{!_LOGIN}" class="inline js-submit-check-username-for-blankness" action="{LOGIN_URL*}" method="post" autocomplete="on">
						{$INSERT_SPAMMER_BLACKHOLE}

						<input type="hidden" name="_active_login" value="1" />

						<div>
							<div class="accessibility-hidden"><label for="member-bar-login-username">{$LOGIN_LABEL}</label></div>
							<div class="accessibility-hidden"><label for="member-bar-s-password">{!PASSWORD}</label></div>
							<input size="15" type="text" placeholder="{!USERNAME}" id="member-bar-login-username" class="form-control" name="username" autocomplete="username" />
							<input size="15" type="password" placeholder="{!PASSWORD}" name="password" autocomplete="current-password" id="member-bar-s-password" class="form-control" />
							{+START,IF,{$CONFIG_OPTION,password_cookies}}
								<label for="remember">{!REMEMBER_ME}:</label>
								<input class="{+START,IF,{$NOT,{$CONFIG_OPTION,remember_me_by_default}}}js-click-confirm-remember-me{+END}"{+START,IF,{$CONFIG_OPTION,remember_me_by_default}} checked="checked"{+END} type="checkbox" value="1" id="remember" name="remember" />
							{+END}
							<button class="btn btn-primary btn-scri menu--site-meta--user-actions--login" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} {!_LOGIN}</button>

							{+START,IF_EMPTY,{$FB_CONNECT_UID}}{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}{+START,IF,{$CONFIG_OPTION,facebook_allow_signups}}
								<div class="fb-login-button" data-scope="email{$,Asking for this stuff is now a big hassle as it needs a screencast(s) making: user_birthday,user_about_me,user_hometown,user_location,user_website}{+START,IF,{$CONFIG_OPTION,facebook_auto_syndicate}},publish_actions,publish_pages{+END}"></div>
							{+END}{+END}{+END}
							<ul class="horizontal-links associated-links-block-group">
								<li><a href="{JOIN_URL*}">{!_JOIN}</a></li>
								<li><a data-open-as-overlay="{}" rel="nofollow" href="{FULL_LOGIN_URL*}" title="{!MORE}: {!_LOGIN}">{!MORE}</a></li>
							</ul>
						</div>
					</form>
				</div>
				{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}}
					<div class="cns-guest-column cns-guest-column-c">
						{+START,INCLUDE,MEMBER_BAR_SEARCH}{+END}
					</div>
				{+END}{+END}

				<nav class="cns-guest-column cns-member-column-d">
					{$,<p class="cns-member-column-title">{!VIEW}:</p>}
					<ul class="actions-list">
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a data-open-as-overlay="{}" href="{NEW_POSTS_URL*}">{!POSTS_SINCE}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a data-open-as-overlay="{}" href="{UNANSWERED_TOPICS_URL*}">{!UNANSWERED_TOPICS}</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
</section>
