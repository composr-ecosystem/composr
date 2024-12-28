{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<span data-tpl="formScreenInputGroup" class="autocomplete-wrapper">
	<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} {+START,IF,{$EQ,{NAME},edit_group}}{+START,IF,{$MOBILE}} autocorrect="off"{+END}{+END} maxlength="255" tabindex="{TABINDEX*}" class="form-control {+START,IF,{NEEDS_MATCH}}input-group{+END}{+START,IF,{$NOT,{NEEDS_MATCH}}}input-line{+END}{REQUIRED*} js-focus-update-ajax-group-list js-keyup-update-ajax-group-list" type="text" id="{NAME*}" name="{NAME*}" value="{DEFAULT*}" />
</span>
