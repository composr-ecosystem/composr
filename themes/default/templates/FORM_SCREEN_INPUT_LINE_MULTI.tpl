<div class="constrain_field">
	<div>
		<div class="accessibility_hidden"><label for="{NAME_STUB*}{I*}">{PRETTY_NAME*}</label></div>
		<input{+START,IF_PASSED,MAXLENGTH} maxlength="{MAXLENGTH*}"{+END}{+START,IF_NON_PASSED,MAXLENGTH} maxlength="255"{+END} tabindex="{TABINDEX*}" class="input_{CLASS*}{REQUIRED*}{+START,IF,{$NEQ,{CLASS},email}} wide_field{+END}" size="{$?,{$MOBILE},27,40}" onkeypress="_ensure_next_field(event,this);" type="{$?,{$EQ,{CLASS},integer},number,text}" id="{$REPLACE,[],_,{NAME_STUB*}}{I*}" name="{NAME_STUB*}{+START,IF,{$NOT,{$IN_STR,{NAME_STUB},[]}}}{I*}{+END}" value="{DEFAULT*}"{+START,IF_PASSED,PATTERN} pattern="{PATTERN*}"{+END} />
		<input type="hidden" name="label_for__{NAME_STUB*}{I*}" value="{PRETTY_NAME*}" />
	</div>
</div>

