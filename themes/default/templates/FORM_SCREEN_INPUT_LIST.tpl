{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$SET,delimiter,}
{$SET,image_sources,\{{+START,IF_PASSED,IMAGES}{+START,LOOP,IMAGES}{$GET,delimiter}"{_loop_var#/}" : "{$IMG#/,{_loop_var}}"{$SET,delimiter,\,}{+END}{+END}\}}

<div class="form-screen-input-list" data-tpl="formScreenInputList" data-tpl-params="{+START,PARAMS_JSON,INLINE_LIST,IMAGES,NAME,image_sources}{_*}{+END}">
	<select {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} {$?,{INLINE_LIST},size="{SIZE*}"} tabindex="{TABINDEX*}" class="form-control input-list{REQUIRED*} {$?,{INLINE_LIST},form-control-wide}{+START,IF_PASSED,ON_CHANGE} js-onchange-{ON_CHANGE*}{+END}" id="{NAME*}" name="{NAME*}"{+START,IF,{READ_ONLY}} disabled="disabled"{+END} data-submit-on-enter="1">
		{CONTENT}
	</select>
</div>