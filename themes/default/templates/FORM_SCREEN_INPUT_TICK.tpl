{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{+START,IF,{$EQ,{NAME},validated}}<span class="validated-checkbox{+START,IF,{CHECKED}} checked{+END}"><span class="on">{!ON}</span><span class="off">{!OFF}</span></span>{+END}
<input type="checkbox"{+START,IF,{READ_ONLY}} data-click-pd="1"{+END} class="input-tick" id="{NAME*}" name="{+START,IF,{DISABLED}}disabled__{+END}{NAME*}" data-tpl="formScreenInputTick" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}" tabindex="{TABINDEX*}" value="{+START,IF_PASSED,VALUE}{VALUE*}{+END}{+START,IF_NON_PASSED,VALUE}1{+END}"{+START,IF,{CHECKED}} checked="checked"{+END}{+START,IF,{DISABLED}} disabled="disabled"{+END} />
{+START,IF,{DISABLED}}
	<input name="{NAME*}" value="{VALUE*}" type="hidden" />
{+END}
<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
