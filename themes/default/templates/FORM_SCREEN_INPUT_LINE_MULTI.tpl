{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{+START,LOOP,DEFAULT_ARRAY}
	<div data-tpl="formScreenInputLineMulti" class="multi-field">
		<div class="accessibility-hidden"><label for="{NAME_STUB*}{I*}">{PRETTY_NAME*}</label></div>
		<input {+START,IF_PASSED,MAXLENGTH} maxlength="{MAXLENGTH*}"{+END}{+START,IF_NON_PASSED,MAXLENGTH} maxlength="255"{+END} tabindex="{TABINDEX*}" class="form-control {+START,IF,{$NEQ,{CLASS},email}}form-control-wide{+END} input-{$REPLACE,_,-,{CLASS*}}{REQUIRED*} js-keypress-ensure-next-field js-input-change-ensure-acceptable-value" size="{$?,{$MOBILE},34,40}" type="{$?,{$EQ,{CLASS},integer},number,text}" id="{$REPLACE,[],_,{NAME_STUB*}}{I*}" name="{NAME_STUB*}{+START,IF,{$NOT,{$IN_STR,{NAME_STUB},[]}}}{I*}{+END}" value="{NAME*}"{+START,IF_PASSED,PATTERN} pattern="{PATTERN*}"{+END}{+START,IF,{READONLY}} readonly{+END} />
		<input type="hidden" name="label_for__{NAME_STUB*}{I*}" value="{PRETTY_NAME*}" />
	</div>
{+END}

{+START,IF_PASSED,NUM_REQUIRED}
	<input type="hidden" id="{NAME_STUB*}-num_required" name="num_required" value="{NUM_REQUIRED*}" />
{+END}
