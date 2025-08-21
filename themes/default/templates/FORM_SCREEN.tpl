{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div data-tpl="formScreen" data-tpl-params="{+START,PARAMS_JSON,URL,IFRAME_URL,SKIPPABLE,JS_FUNCTION_CALLS}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{+START,IF_NON_EMPTY,{TEXT}}
		<div class="form-text">{$PARAGRAPH,{TEXT}}</div>
	{+END}

	{+START,IF,{$IN_STR,{FIELDS},required-star}}
		{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
	{+END}

	{+START,IF_NON_PASSED,IFRAME_URL}
	<form title="{!PRIMARY_PAGE_FORM}" id="main-form"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+START,IF,{$IN_STR,{FIELDS},"file"}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}{+START,IF_NON_PASSED,TARGET} target="_top"{+END}{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} data-submit-modsecurity-workaround="1"{+END} data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME=main-form{+END}">
		{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

		{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}
	{+END}
	{+START,IF_PASSED,IFRAME_URL}
	<form title="{!PRIMARY_PAGE_FORM}" id="main-form"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{IFRAME_URL*}"{+START,IF,{$IN_STR,{FIELDS},"file"}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{IFRAME_URL}}"{+END} target="iframe-under" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME=main-form{+END}">
		{$INSERT_FORM_POST_SECURITY}

		{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{IFRAME_URL}}{+END}
	{+END}

		{+START,IF_PASSED,SKIPPABLE}
			<div class="skip-step-button-wrap{+START,IF,{$IN_STR,{FIELDS},required-star}} skip-step-button-wrap-with-req-note{+END}">
				<div>
					<input type="hidden" id="{SKIPPABLE*}" name="{SKIPPABLE*}" value="0" />
					<button data-disable-on-click="1" tabindex="151" class="btn btn-primary btn-scri buttons--skip js-btn-skip-step" type="submit">{+START,INCLUDE,ICON}NAME=buttons/skip{+END} {!SKIP}</button>
				</div>
			</div>
		{+END}

		<div>
			{HIDDEN}

			{+START,IF_NON_EMPTY,{FIELDS}}
				<table class="map-table form-table wide-table scrollable-inside">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="field-name-column" />
							<col class="field-input-column" />
						</colgroup>
					{+END}

					<tbody>
						{FIELDS}
					</tbody>
				</table>
			{+END}

			{+START,INCLUDE,FORM_STANDARD_END}{+END}
		</div>
	</form>

	{+START,IF_PASSED,IFRAME_URL}
		<a id="edit-space"></a>

		<div class="arrow-ruler">
			<form action="#!" method="post">
				<div class="associated-link">
					<input type="checkbox" name="will_open_new" id="will_open_new" class="js-checkbox-will-open-new" />
					<label for="will_open_new">{!CHOOSE_OPEN_NEW_WINDOW}</label>
				</div>
			</form>

			<img alt="" width="45" height="45" src="{$IMG*,arrow_ruler}" />
		</div>

		<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} class="form-screen-iframe" title="{!EDIT}" name="iframe-under" id="iframe-under" src="{$BASE_URL*}/data/empty.php">{!EDIT}</iframe>
	{+END}
</div>