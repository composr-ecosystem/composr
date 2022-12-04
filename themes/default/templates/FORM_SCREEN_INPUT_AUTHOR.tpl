{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div data-tpl="formScreenInputAuthor" class="form-screen-input-author autocomplete-wrapper">
	<span class="invisible-ref-point"></span>
	<input {+START,IF,{$MOBILE}} autocorrect="off"{+END} {+START,IF_PASSED,AUTOCOMPLETE}autocomplete="{AUTOCOMPLETE*}"{+END} size="27" maxlength="80" tabindex="{TABINDEX*}" class="input-author{REQUIRED*} js-keyup-update-ajax-author-list form-control form-control-wide" type="text" id="{NAME*}" name="{NAME*}" value="{DEFAULT*}" />
</div>
