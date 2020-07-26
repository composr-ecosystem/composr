{$SET,ajax_block_main_newsletter_signup_wrapper,ajax_block_main_newsletter_signup_wrapper_{$RAND%}}

<div id="{$GET*,ajax_block_main_newsletter_signup_wrapper}" class="box_wrapper">
	{+START,IF_PASSED,MSG}
		<p>
			{MSG}
		</p>
	{+END}

	<section class="box box___block_main_newsletter_signup"><div class="box_inner">
		<h3>{!NEWSLETTER}{$?,{$NEQ,{NEWSLETTER_TITLE},{!GENERAL}},: {NEWSLETTER_TITLE*}}</h3>

		<form title="{!NEWSLETTER}" onsubmit="if (!check_field_for_blankness(this.elements['address{NID;*}'],event)) return false; if (!this.elements['address{NID*}'].value.match(/^[a-zA-Z0-9\._\-\+]+@[a-zA-Z0-9\._\-]+$/)) { window.fauxmodal_alert('{!javascript:NOT_A_EMAIL;=*}'); return false; } disable_button_just_clicked(this); return true;" action="{URL*}" method="post" autocomplete="off" target="_self">
			{$INSERT_SPAMMER_BLACKHOLE}

			{+START,IF_NON_PASSED_OR_FALSE,BUTTON_ONLY}
				<p class="accessibility_hidden"><label for="baddress">{!EMAIL_ADDRESS}</label></p>

				<div class="constrain_field">
					<input class="wide_field field_input_non_filled" id="baddress" name="address{NID*}" onfocus="placeholder_focus(this);" onblur="placeholder_blur(this);" alt="{!EMAIL_ADDRESS}" value="{!EMAIL_ADDRESS}" />
				</div>
			{+END}

			<p class="proceed_button">
				<input class="button_screen_item menu__site_meta__newsletters" type="submit" value="{!SUBSCRIBE}" />
			</p>
		</form>
	</div></section>

	{$REQUIRE_JAVASCRIPT,ajax}
	{$REQUIRE_JAVASCRIPT,checking}

	<script>// <![CDATA[
		add_event_listener_abstract(window,'load',function() {
			internalise_ajax_block_wrapper_links('{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS}}',document.getElementById('{$GET;,ajax_block_main_newsletter_signup_wrapper}'),[],{ },false,true);
		});
	//]]></script>
</div>
