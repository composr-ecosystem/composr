{$SET,ajax_block_main_newsletter_signup_wrapper,ajax_block_main_newsletter_signup_wrapper_{$RAND%}}

{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,newsletter}

<div id="{$GET*,ajax_block_main_newsletter_signup_wrapper}" data-ajaxify="{ callUrl: '{$FACILITATE_AJAX_BLOCK_CALL;*,{BLOCK_PARAMS}}', callParamsFromTarget: ['.*'], targetsSelector: '.js-form-newsletter-email-subscribe' }">
	{+START,IF_PASSED,MSG}
		<p>{MSG}</p>
	{+END}

	<section class="box box---block-main-newsletter-signup" data-tpl="blockMainNewsletterSignup" data-tpl-params="{+START,PARAMS_JSON,NID}{_*}{+END}"><div class="box-inner">
		<h3>{!NEWSLETTER}{$?,{$NEQ,{NEWSLETTER_TITLE},{!GENERAL}},: {NEWSLETTER_TITLE*}}</h3>

		<form title="{!NEWSLETTER}" action="{URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			{+START,IF_NON_PASSED_OR_FALSE,BUTTON_ONLY}
				<div class="form-group">
					<!--
					<p class="accessibility-hidden"><label for="bforename">{!FORENAME}</label></p>
					<p><input class="form-control form-control-wide" id="bforename" name="forename{NID*}" autocomplete="given-name" placeholder="{!FORENAME}" /></p>

					<p class="accessibility-hidden"><label for="bsurname">{!FORENAME}</label></p>
					<p><input class="form-control form-control-wide" id="bsurname" name="surname{NID*}" autocomplete="family-name" placeholder="{!SURNAME}" /></p>
					-->

					<p class="accessibility-hidden"><label for="baddress">{!EMAIL_ADDRESS}</label></p>
					<p><input class="form-control form-control-wide" id="baddress" name="address{NID*}" autocomplete="email" placeholder="{!EMAIL_ADDRESS}" /></p>
				</div>

				<div class="form-group">
					{EXTRA_HIDDEN}
					{EXTRA_FIELDS}
				</div>
			{+END}

			<p class="proceed-button">
				<button class="btn btn-primary btn-scri js-newsletter-email-subscribe" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/newsletters{+END} <span>{!SUBSCRIBE}</span></button>
			</p>
		</form>
	</div></section>
</div>
