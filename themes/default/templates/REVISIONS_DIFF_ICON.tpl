{$REQUIRE_JAVASCRIPT,actionlog}
{+START,SET,tooltip}
	{+START,IF_EMPTY,{RENDERED_DIFF}}<em>{!DIFF_NONE;}</em>{+END}
	{$?,{$LT,{$LENGTH,{RENDERED_DIFF}},5000},<div class="diff">{$REPLACE,\\n,<br />,{RENDERED_DIFF;}}</div>,<em>{!DIFF_TOO_MUCH;}</em>}
{+END}

{+START,IF,{$DESKTOP}}
	<span class="inline-desktop">
		<a data-tpl="revisionsDiffIcon" class="button-icon" data-cms-tooltip="{ contents: '{$GET;^*,tooltip}', width: '500px', delay: 0, position: 'bottom' }" href="#!">{+START,INCLUDE,ICON}
			NAME=help
			ICON_SIZE=24
		{+END}</a>
	</span>
{+END}
<div class="block-mobile">
	{$GET,tooltip}
</div>
