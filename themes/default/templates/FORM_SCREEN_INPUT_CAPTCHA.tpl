{$REQUIRE_JAVASCRIPT,captcha}

{$SET,early_description,1}

{+START,IF_EMPTY,{$CONFIG_OPTION,recaptcha_site_key}}
	{+START,SET,CAPTCHA}
		<div class="captcha">
			{+START,IF,{$CONFIG_OPTION,audio_captcha,1}}
				<a id="captcha-audio" rel="nofollow" class="js-click-play-self-audio-link" title="{!captcha:PLAY_AUDIO_VERSION}" href="{$CUSTOM_BASE_URL*}/uploads/captcha/{$SESSION*}.wav?cache_break={$RAND&*}">{!captcha:PLAY_AUDIO_VERSION}</a>
			{+END}
			{+START,IF,{$CONFIG_OPTION,css_captcha,1}}
				<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} tabindex="-1" id="captcha-readable" class="captcha-frame" title="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" src="{$FIND_SCRIPT*,captcha}?cache_break={$RAND&*}{$KEEP*,0,1}">{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}</iframe>
			{+END}
			{+START,IF,{$NOT,{$CONFIG_OPTION,css_captcha,1}}}
				<a data-open-as-overlay="{}" rel="nofollow" href="{$FIND_SCRIPT*,captcha}?cache_break={$RAND&*}&mode=large{$KEEP*,0,1}" title="{!captcha:CAPTCHA_CLICK_LARGE}" aria-haspopup="dialog">
					<img id="captcha-readable" title="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" alt="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" src="{$FIND_SCRIPT*,captcha}?cache_break={$RAND&*}{$KEEP*,0,1}" />
				</a>
			{+END}
			<div class="accessibility-hidden"><label for="captcha">{!captcha:AUDIO_CAPTCHA_HELP}</label></div>
			<input {+START,IF_PASSED,TABINDEX} tabindex="{TABINDEX*}"{+END} maxlength="6" size="8" class="form-control input-text-required" type="text" id="captcha" name="captcha" />
		</div>
	{+END}

	<div class="input-captcha" data-tpl="inputCaptcha" data-tpl-params="{+START,PARAMS_JSON,CAPTCHA}{_*}{+END}">
		{+START,IF,{$CONFIG_OPTION,js_captcha}}
			<div id="captcha-spot"></div>
		{+END}
		{+START,IF,{$NOT,{$CONFIG_OPTION,js_captcha}}}
			{$GET,CAPTCHA}
		{+END}
	</div>
{+END}

{+START,IF_NON_EMPTY,{$CONFIG_OPTION,recaptcha_site_key}}
	<div data-recaptcha-captcha="true" id="captcha"{+START,IF_PASSED,TABINDEX} data-tabindex="{TABINDEX*}"{+END}></div>
{+END}
