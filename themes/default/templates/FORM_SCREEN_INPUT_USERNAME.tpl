{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<span data-tpl="formScreenInputUsername" class="autocomplete-wrapper">
	<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} {+START,IF,{$EQ,{NAME},edit_username}}{+START,IF,{$MOBILE}} autocorrect="off"{+END}{+END} maxlength="255" tabindex="{TABINDEX*}" class="form-control {+START,IF,{NEEDS_MATCH}}input-username{+END}{+START,IF,{$NOT,{NEEDS_MATCH}}}input-line{+END}{REQUIRED*} js-focus-update-ajax-member-list js-keyup-update-ajax-member-list" type="text" id="{NAME*}" name="{NAME*}" value="{DEFAULT*}" />
</span>
