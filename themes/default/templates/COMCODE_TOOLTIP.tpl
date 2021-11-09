{+START,IF_NON_EMPTY,{URL}}
	<a title="{$STRIP_TAGS,{TOOLTIP}} {!LINK_NEW_WINDOW}" target="_blank" class="non_link" href="{URL*}"><span class="comcode_concept_inline" onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{TOOLTIP;^*}','700px');">{CONTENT}</span></a>
{+END}
{+START,IF_EMPTY,{URL}}
	<span title="{$STRIP_TAGS,{TOOLTIP}}" class="comcode_concept_inline" onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{TOOLTIP;^*}','700px');">{CONTENT}</span>
{+END}
