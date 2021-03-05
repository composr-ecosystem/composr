{$REQUIRE_JAVASCRIPT,ecommerce}

<form data-tpl="purchaseWizardStageTerms" title="{!PRIMARY_PAGE_FORM}"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}>
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

	<p>{!AGREEMENT_PROCESS}</p>

	<div class="agreement-box">
		<p class="lonely-label">{!AGREEMENT}:</p>

		<div class="purchase-terms">{TERMS*}</div>
	</div>

	<p>
		<input type="checkbox" id="confirm" name="confirm" value="1" class="js-checkbox-click-toggle-proceed-btn" /><label for="confirm">{!I_AGREE}</label>
	</p>

	<p>
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--yes" type="submit" disabled="disabled" id="proceed-button">{+START,INCLUDE,ICON}NAME=buttons/yes{+END} {!PROCEED}</button>

		<button type="button" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--no js-click-btn-i-disagree" data-tp-location="{$PAGE_LINK*,:}">{+START,INCLUDE,ICON}NAME=buttons/no{+END} {!I_DISAGREE}</button>
	</p>
</form>
