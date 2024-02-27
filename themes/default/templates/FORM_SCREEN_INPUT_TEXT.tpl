{$REQUIRE_JAVASCRIPT,editing}

<div id="container-for-{NAME*}" class="form-screen-input-text" data-tpl="formScreenInputText" data-tpl-params="{+START,PARAMS_JSON,REQUIRED,NAME}{_*}{+END}">
	{+START,IF_NON_PASSED_OR_FALSE,DISPLAY_ONLY}
		<textarea {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} data-textarea-auto-height="" tabindex="{TABINDEX*}" class="input-text{REQUIRED*} form-control form-control-wide{+START,IF,{SCROLLS}} textarea-scroll{+END}" cols="70" rows="{+START,IF_PASSED,ROWS}{ROWS*}{+END}{+START,IF_NON_PASSED,ROWS}7{+END}" id="{NAME*}" name="{NAME*}"{+START,IF_PASSED,MAXLENGTH} maxlength="{MAXLENGTH*}"{+END}>{DEFAULT*}</textarea>
		{+START,IF_PASSED,DEFAULT_PARSED}
			<textarea aria-hidden="true" cols="1" rows="1" style="display: none" readonly="readonly" disabled="disabled" name="{NAME*}_parsed">{DEFAULT_PARSED*}</textarea>
		{+END}
	
		{+START,IF_PASSED_AND_TRUE,RAW}<input type="hidden" name="pre_f_{NAME*}" value="1" />{+END}
	{+END}
	
	{+START,IF_PASSED_AND_TRUE,DISPLAY_ONLY}
		{$PARAGRAPH,{DEFAULT}}
	{+END}
</div>
