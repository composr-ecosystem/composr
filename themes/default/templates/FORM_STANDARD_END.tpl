<div class="form-standard-end">
	{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}
		{+START,IF_NON_PASSED_OR_FALSE,SKIP_WEBSTANDARDS}{+START,IF,{$OR,{$CONFIG_OPTION,enable_markup_webstandards},{$CONFIG_OPTION,enable_spell_check},{$AND,{$HAS_PRIVILEGE,perform_keyword_check},{$CONFIG_OPTION,enable_keyword_density_check}}}}
			<div class="preview-checking-box">
				<section class="box box---form-standard-end"><div class="box-inner">
					<h3>{!PERFORM_CHECKS_ON_PREVIEW}</h3>

					{+START,IF,{$CONFIG_OPTION,enable_markup_webstandards}}
						<p>
							<span class="field-name">{!WEBSTANDARDS}:</span>
							<label for="perform_webstandards_check_no"><input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_0}"{+START,IF,{$NOT,{$HAS_PRIVILEGE,perform_webstandards_check_by_default}}} checked="checked"{+END} type="radio" name="perform_webstandards_check" value="0" id="perform_webstandards_check_no" /> {!NO}</label>
							<label for="perform_webstandards_check_yes"><input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_1}"{+START,IF,{$HAS_PRIVILEGE,perform_webstandards_check_by_default}} checked="checked"{+END} type="radio" name="perform_webstandards_check" value="1" id="perform_webstandards_check_yes" /> {!YES}</label>
							<label for="perform_webstandards_check_more"><input title="{!DESCRIPTION_WEBSTANDARDS_ON_PREVIEW_2}" type="radio" name="perform_webstandards_check" value="2" id="perform_webstandards_check_more" /> {!MANUAL_CHECKS_TOO}</label>
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
			<button class="btn btn-primary btn-scr buttons--back" type="button" data-cms-btn-go-back="1">{+START,INCLUDE,ICON}NAME=buttons/back{+END} <span>{!GO_BACK}</span></button>
		{+END}
		{+START,IF_PASSED,BACK_URL}
			<button class="btn btn-primary btn-scr buttons--back js-click-btn-go-back" type="button">{+START,INCLUDE,ICON}NAME=buttons/back{+END} <span>{!GO_BACK}</span></button>
		{+END}

		{+START,IF_PASSED,EXTRA_BUTTONS}{EXTRA_BUTTONS}{+END}
		{+START,IF_PASSED,CANCEL_URL}
			<button class="btn btn-primary btn-scr buttons--cancel js-click-do-form-cancel" type="button">{+START,INCLUDE,ICON}NAME=buttons/cancel{+END} <span>{!INPUTSYSTEM_CANCEL}</span></button>
		{+END}
		{+START,IF_PASSED_AND_TRUE,PREVIEW}{+START,IF,{$CONFIG_OPTION,enable_previews}}
			<button class="btn btn-primary btn-scr buttons--preview js-click-do-form-preview" id="preview-button" accesskey="p" tabindex="{+START,IF_PASSED,TABINDEX}{TABINDEX}{+END}{+START,IF_NON_PASSED,TABINDEX}250{+END}" type="button">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} <span>{!PREVIEW}</span></button>
		{+END}{+END}
		<button class="btn btn-primary btn-scr js-btn-main-submit-form"{+START,IF_NON_PASSED_OR_FALSE,SECONDARY_FORM}{+START,IF_NON_PASSED,BUTTON_ID} id="submit-button"{+END} accesskey="u"{+END}{+START,IF_PASSED,BUTTON_ID} id="{BUTTON_ID*}"{+END} tabindex="{+START,IF_PASSED,TABINDEX}{TABINDEX}{+END}{+START,IF_NON_PASSED,TABINDEX}250{+END}" type="submit">{+START,INCLUDE,ICON}NAME={SUBMIT_ICON}{+END} {SUBMIT_NAME*}</button>
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
