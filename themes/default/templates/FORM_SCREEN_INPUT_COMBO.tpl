{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div class="inline" data-tpl="formScreenInputCombo" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}">
	<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} class="form-control input-line{REQUIRED*} js-keyup-toggle-fallback-list" tabindex="{TABINDEX*}" type="text" value="{DEFAULT*}" id="{NAME*}" name="{NAME*}" list="{NAME*}-list" />
	<datalist id="{NAME*}-list">
		<label for="{NAME*}-fallback-list" class="associated-details">{!fields:OR_ONE_OF_THE_BELOW}:</label>
		<select size="5" name="{NAME*}" id="{NAME*}-fallback-list" class="form-control input-list" style="display: block; width: 14em">{$,select is for non-datalist-aware browsers}
			{CONTENT}
		</select>
	</datalist>
</div>
