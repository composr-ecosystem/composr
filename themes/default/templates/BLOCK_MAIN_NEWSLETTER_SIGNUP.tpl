{$SET,ajax_block_main_newsletter_signup_wrapper,ajax_block_main_newsletter_signup_wrapper_{$RAND%}}

{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,newsletter}

<div id="{$GET*,ajax_block_main_newsletter_signup_wrapper}" data-ajaxify="{ callUrl: '{$FACILITATE_AJAX_BLOCK_CALL;*,{BLOCK_PARAMS}}', callParamsFromTarget: ['.*'], targetsSelector: '.js-form-newsletter-email-subscribe' }">
	{+START,IF_PASSED,MSG}
		<p>{MSG}</p>
	{+END}

	<section class="box box---block-main-newsletter-signup" data-tpl="blockMainNewsletterSignup" data-tpl-params="{+START,PARAMS_JSON,NID}{_*}{+END}"><div class="box-inner">
		<h3>{!NEWSLETTER}{$?,{$NEQ,{NEWSLETTER_TITLE},{!GENERAL}},: {NEWSLETTER_TITLE*}}</h3>

		<form class="js-form-newsletter-email-subscribe" title="{!NEWSLETTER}" action="{URL*}" method="post">
			{$INSERT_SPAMMER_BLACKHOLE}

			{+START,IF_NON_PASSED_OR_FALSE,BUTTON_ONLY}
				<p class="accessibility-hidden"><label for="baddress">{!EMAIL_ADDRESS}</label></p>

				<div class="form-group">
					<input class="form-control form-control-wide" id="baddress" name="address{NID*}" autocomplete="email" placeholder="{!EMAIL_ADDRESS}" />
				</div>
			{+END}

			<p class="proceed-button">
				<button class="btn btn-primary btn-scri" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/newsletters{+END} {!SUBSCRIBE}</button>
			</p>
		</form>
	</div></section>
</div>
