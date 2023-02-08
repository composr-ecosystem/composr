{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<span data-tpl="formScreenInputPassword" data-tpl-params="{+START,PARAMS_JSON,VALUE,NAME}{_*}{+END}">
	{+START,IF,{PASSWORD_STRENGTH}}
		<span style="display: none" id="password-strength-{NAME*}" class="password-strength js-mouseover-activate-password-strength-tooltip">
			<span class="password-strength-inner"></span>
		</span>
	{+END}

	<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} {+START,IF,{$EQ,{NAME},edit_password}}{+START,IF,{$MOBILE}} autocorrect="off"{+END}{+END} size="27" maxlength="255" tabindex="{TABINDEX*}" class="form-control input-password{REQUIRED*}{+START,IF,{PASSWORD_STRENGTH}} js-input-change-check-password-strength{+END}" type="password" id="{NAME*}" name="{NAME*}" value="{VALUE*}" />
</span>
