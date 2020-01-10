{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<select tabindex="{TABINDEX*}" class="form-control input-list-required" id="{NAME*}__start" name="{NAME*}__start" data-submit-on-enter="1" data-tpl="formScreenInputList" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}">
	{+START,LOOP,MONTHS}
		<option value="{_loop_key*}"{+START,IF,{$EQ,{START},{_loop_key}}} selected="selected"{+END}>{_loop_var*}</option>
	{+END}
</select>

&ndash;

<select tabindex="{TABINDEX*}" class="form-control input-list-required" id="{NAME*}__end" name="{NAME*}__end" data-submit-on-enter="1" data-tpl="formScreenInputList" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}">
	{+START,LOOP,MONTHS}
		<option value="{_loop_key*}"{+START,IF,{$EQ,{END},{_loop_key}}} selected="selected"{+END}>{_loop_var*}</option>
	{+END}
</select>
