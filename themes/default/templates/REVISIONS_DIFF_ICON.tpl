<ul class="horizontal-links">
	<li>
		{+START,SET,tooltip}
			{+START,IF_EMPTY,{RENDERED_DIFF_IMMEDIATELY_AFTER}}<em>{!DIFF_NONE;}</em>{+END}
			{$?,{$LT,{$LENGTH,{RENDERED_DIFF_IMMEDIATELY_AFTER}},5000},<div class="diff">{$REPLACE,\\n,<br />,{RENDERED_DIFF_IMMEDIATELY_AFTER;}}</div>,<em>{!DIFF_TOO_MUCH;}</em>}
		{+END}
		<a class="leave-native-tooltip" data-cms-tooltip="{ contents: '{$GET;^*,tooltip}', width: '800px', delay: 0, position: 'bottom' }" href="{DIFF_IMMEDIATELY_AFTER_URL*}" target="_blank" title="{!DIFF_IMMEDIATELY_AFTER} {!LINK_NEW_WINDOW}">{!DIFF_IMMEDIATELY_AFTER}</a>
	</li>

	<li>
		{+START,SET,tooltip}
			{+START,IF_EMPTY,{RENDERED_DIFF_EVERYTHING_AFTER}}<em>{!DIFF_NONE;}</em>{+END}
			{$?,{$LT,{$LENGTH,{RENDERED_DIFF_EVERYTHING_AFTER}},5000},<div class="diff">{$REPLACE,\\n,<br />,{RENDERED_DIFF_EVERYTHING_AFTER;}}</div>,<em>{!DIFF_TOO_MUCH;}</em>}
		{+END}
		<a class="leave-native-tooltip" data-cms-tooltip="{ contents: '{$GET;^*,tooltip}', width: '800px', delay: 0, position: 'bottom' }" href="{DIFF_EVERYTHING_AFTER_URL*}" target="_blank" title="{!DIFF_EVERYTHING_AFTER} {!LINK_NEW_WINDOW}">{!DIFF_EVERYTHING_AFTER}</a>
	</li>
</ul>
