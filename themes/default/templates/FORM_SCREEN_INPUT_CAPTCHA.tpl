{+START,SET,CAPTCHA}
	<div class="captcha">
		{+START,IF,{$CONFIG_OPTION,audio_captcha}}
			<a rel="nofollow" onclick="return play_self_audio_link(this,captcha_sound);" title="{!captcha:PLAY_AUDIO_VERSION}" href="{$CUSTOM_BASE_URL*}/uploads/auto_thumbs/{$SESSION*}.wav?cache_break={$RAND&*}">{!captcha:PLAY_AUDIO_VERSION}</a>
		{+END}
		{+START,IF,{$CONFIG_OPTION,css_captcha}}
			<iframe{$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} id="captcha_readable" class="captcha_frame" title="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" src="{$FIND_SCRIPT*,captcha}?cache_break={$RAND&*}{$KEEP*,0,1}">{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}</iframe>
		{+END}
		{+START,IF,{$NOT,{$CONFIG_OPTION,css_captcha}}}
			<img id="captcha_readable" title="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" alt="{!CONTACT_STAFF_TO_JOIN_IF_IMPAIRED}" src="{$FIND_SCRIPT*,captcha}?cache_break={$RAND&*}{$KEEP*,0,1}" />
		{+END}
	</div>
	<div class="accessibility_hidden"><label for="captcha">{!captcha:AUDIO_CAPTCHA_HELP}</label></div>
	<input{+START,IF_PASSED,TABINDEX} tabindex="{TABINDEX*}"{+END} maxlength="6" size="8" class="input_text_required" value="" type="text" id="captcha" name="captcha" />

	<script>// <![CDATA[
		var captcha_sound=(typeof window.Audio!='undefined')?new Audio('{$CUSTOM_BASE_URL*}/uploads/auto_thumbs/{$SESSION*}.wav?cache_break={$RAND&*}'):null;

		var showevent=(typeof window.onpageshow!='undefined')?'pageshow':'load';

		var func=function() {
			document.getElementById('captcha_readable').src+='&'; // Force it to reload latest captcha
		};

		if (typeof window.addEventListener!='undefined')
		{
			window.addEventListener(showevent,func,false);
		}
		else if (typeof window.attachEvent!='undefined')
		{
			window.attachEvent('on'+showevent,func);
		}
	//]]></script>
{+END}

{+START,IF,{$CONFIG_OPTION,js_captcha}}
	<noscript>{!JAVASCRIPT_REQUIRED}</noscript>

	<div id="captcha_spot"></div>
	<script>// <![CDATA[
		set_inner_html(document.getElementById('captcha_spot'),'{$GET;^/,CAPTCHA}');
	//]]></script>
{+END}
{+START,IF,{$NOT,{$CONFIG_OPTION,js_captcha}}}
	{$GET,CAPTCHA}
{+END}
