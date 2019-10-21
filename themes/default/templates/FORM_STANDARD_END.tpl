{+START,IF_PASSED_AND_TRUE,SUPPORT_AUTOSAVE}{+START,IF_PASSED,FORM_NAME}
	{$REQUIRE_JAVASCRIPT,posting}
{+END}{+END}

{$SET,preview_url,{$PREVIEW_URL}{$KEEP}{+START,IF_PASSED,THEME}&utheme={THEME}{+END}}
{$SET,force_previews,0}
{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}{+START,IF,{$FORCE_PREVIEWS}}
	{$SET,force_previews,1}
{+END}{+END}{+END}

{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div class="form-standard-end" data-view="FormStandardEnd" data-view-params="{+START,PARAMS_JSON,preview_url,force_previews,JS_FUNCTION_CALLS,JAVASCRIPT,SECONDARY_FORM,SUPPORT_AUTOSAVE,FORM_NAME,SEPARATE_PREVIEW,BACK_URL,CANCEL_URL,ANALYTIC_EVENT_CATEGORY}{_*}{+END}">
	{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}
		{+START,IF_NON_PASSED_OR_FALSE,SKIP_WEBSTANDARDS}{+START,IF,{$OR,{$CONFIG_OPTION,enable_markup_webstandards},{$CONFIG_OPTION,enable_spell_check},{$AND,{$HAS_PRIVILEGE,perform_keyword_check},{$CONFIG_OPTION,enable_keyword_density_check}}}}
			<div class="preview-checking-box">
				<section class="box box---form-standard-end"><div class="box-inner">
					<h3>{!PERFORM_CHECKS_ON_PREVIEW}</h3>

					{+START,IF,{$CONFIG_OPTION,enable_markup_webstandards}}
						<p>
							<span class="field-name">{!WEBSTANDARDS}:</span>
							<input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_0}"{+START,IF,{$NOT,{$HAS_PRIVILEGE,perform_webstandards_check_by_default}}} checked="checked"{+END} type="radio" name="perform_webstandards_check" value="0" id="perform_webstandards_check_no" /><label for="perform_webstandards_check_no">{!NO}</label>
							<input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_1}"{+START,IF,{$HAS_PRIVILEGE,perform_webstandards_check_by_default}} checked="checked"{+END} type="radio" name="perform_webstandards_check" value="1" id="perform_webstandards_check_yes" /><label for="perform_webstandards_check_yes">{!YES}</label>
							<input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_2}" type="radio" name="perform_webstandards_check" value="2" id="perform_webstandards_check_more" /><label for="perform_webstandards_check_more">{!MANUAL_CHECKS_TOO}</label>
						</p>
					{+END}
					{+START,IF,{$CONFIG_OPTION,enable_spell_check}}
						<p>
							<label for="perform_spellcheck"><span class="field-name">{!SPELLCHECK}:</span> <input title="{$STRIP_TAGS,{!SPELLCHECK}}" type="checkbox" checked="checked" name="perform_spellcheck" value="1" id="perform_spellcheck" /></label>
						</p>
					{+END}
					{+START,IF,{$CONFIG_OPTION,enable_keyword_density_check}}{+START,IF,{$HAS_PRIVILEGE,perform_keyword_check}}
						<p>
							<label for="perform_keywordcheck"><span class="field-name">{!KEYWORDCHECK}:</span> <input title="{$STRIP_TAGS,{!KEYWORDCHECK}}" type="checkbox" name="perform_keywordcheck" value="1" id="perform_keywordcheck" /></label>
						</p>
					{+END}{+END}
				</div></section>
			</div>
		{+END}{+END}
	{+END}{+END}

	<p class="proceed-button{+START,IF_PASSED,SUBMIT_BUTTON_CLASS} {SUBMIT_BUTTON_CLASS*}{+END}">
		{+START,IF_PASSED_AND_TRUE,BACK}
			<button class="btn btn-primary btn-scr buttons--back" type="button" data-cms-btn-go-back="1">{+START,INCLUDE,ICON}NAME=buttons/back{+END} {!GO_BACK}</button>
		{+END}
		{+START,IF_PASSED,BACK_URL}
			<button class="btn btn-primary btn-scr buttons--back js-click-btn-go-back" type="button">{+START,INCLUDE,ICON}NAME=buttons/back{+END} {!GO_BACK}</button>
		{+END}

		{+START,IF_PASSED,EXTRA_BUTTONS}{EXTRA_BUTTONS}{+END}
		{+START,IF_PASSED,CANCEL_URL}
			<button class="btn btn-primary btn-scr buttons--cancel js-click-do-form-cancel" type="button">{+START,INCLUDE,ICON}NAME=buttons/cancel{+END} {!INPUTSYSTEM_CANCEL}</button>
		{+END}
		{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}
			<button class="btn btn-primary btn-scr buttons--preview js-click-do-form-preview" id="preview-button" accesskey="p" tabindex="{+START,IF_PASSED,TABINDEX}{TABINDEX}{+END}{+START,IF_NON_PASSED,TABINDEX}250{+END}" type="button">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} {!PREVIEW}</button>
		{+END}{+END}
		<button class="btn btn-primary btn-scr js-click-do-form-submit"{+START,IF_NON_PASSED_OR_FALSE,SECONDARY_FORM}{+START,IF_NON_PASSED,BUTTON_ID} id="submit-button"{+END} accesskey="u"{+END}{+START,IF_PASSED,BUTTON_ID} id="{BUTTON_ID*}"{+END} tabindex="{+START,IF_PASSED,TABINDEX}{TABINDEX}{+END}{+START,IF_NON_PASSED,TABINDEX}250{+END}" type="button">{+START,INCLUDE,ICON}NAME={SUBMIT_ICON}{+END} {SUBMIT_NAME*}</button>
	</p>

	{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}
		<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} title="{!PREVIEW}" name="preview-iframe" id="preview-iframe" src="{$BASE_URL*}/data/empty.php" class="hidden-preview-frame">{!PREVIEW}</iframe>
	{+END}{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,IF,{$HAS_PRIVILEGE,see_software_docs}}{+START,IF_PASSED,STAFF_HELP_URL}{+START,IF,{$SHOW_DOCS}}
		{+START,INCLUDE,STAFF_ACTIONS}
			STAFF_ACTIONS_TITLE={!STAFF_ACTIONS}
			1_URL={STAFF_HELP_URL}
			1_TITLE={!HELP}
			1_REL=help
			1_ICON=help
		{+END}
	{+END}{+END}{+END}
</div>
