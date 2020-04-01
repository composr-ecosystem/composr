{+START,IF,{$NOT,{JS_TOOLTIP}}}
	<img width="{WIDTH*}" height="{HEIGHT*}" class="img-thumb" alt="{$STRIP_TAGS,{CAPTION*}}"{+START,IF_NON_EMPTY,{CAPTION}} title="{$STRIP_TAGS,{CAPTION*}}"{+END} src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" />
{+END}
{+START,IF,{JS_TOOLTIP}}
	<img width="{WIDTH*}" height="{HEIGHT*}" class="img-thumb" alt="{$STRIP_TAGS,{CAPTION*}}"{+START,IF_NON_EMPTY,{CAPTION}} data-cms-tooltip="{ contents: '{CAPTION;^=}', width: '40%' }"{+END} src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" />
{+END}
