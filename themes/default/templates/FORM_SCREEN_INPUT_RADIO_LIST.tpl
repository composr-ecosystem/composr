{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$SET,early_description,0}

<div class="radio-list{+START,IF_PASSED_AND_TRUE,IMAGES} radio-list-pictures{+END}{+START,IF_PASSED_AND_TRUE,LINEAR} linear{+END}" data-tpl="formScreenInputRadioList" data-tpl-params="{+START,PARAMS_JSON,NAME,CODE}{_*}{+END}">
	{CONTENT}
</div>

{+START,IF_PASSED,NAME}
	{+START,IF,{REQUIRED}}
		<input type="hidden" name="require__{NAME*}" value="1" />
	{+END}
{+END}
