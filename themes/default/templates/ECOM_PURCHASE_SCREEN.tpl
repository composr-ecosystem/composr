{$REQUIRE_JAVASCRIPT,ecommerce}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{TITLE}

{+START,IF_NON_EMPTY,{URL}}
<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" enctype="multipart/form-data" action="{URL*}"{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END} data-view="SubmissionFlow" data-view-params="{+START,PARAMS_JSON,JS_FUNCTION_CALLS}{_*}{+END}">
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}
{+END}

<div class="purchase-screen-contents">
	{CONTENT}
</div>

{+START,IF_NON_EMPTY,{URL}}
	<p class="purchase-button">
		<button id="proceed-button" class="btn btn-primary btn-scr js-btn-main-submit-form" accesskey="u" type="button">{+START,INCLUDE,ICON}NAME={ICON}{+END} {SUBMIT_NAME*}</button>
	</p>
</form>
{+END}
