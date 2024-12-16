{$REQUIRE_CSS,achievements}

<div class="block-main-achievements">
	{+START,LOOP,ACHIEVEMENTS}
		<div id="achievement_{ACHIEVEMENT_NAME*}" class="block-main-achievements--achievement">
			<a href="{$PAGE_LINK*,_SEARCH:achievements:#achievement_{ACHIEVEMENT_NAME*}}" data-cms-tooltip="{!achievements:ACHIEVEMENT_TOOLTIP*,{ACHIEVEMENT_TITLE},{ACHIEVEMENT_DATE_AND_TIME}}">
				<img style="max-width: {IMAGE_SIZE*}px;" src="{ACHIEVEMENT_IMAGE}" alt="{ACHIEVEMENT_TITLE*}" />
			</a>
		</div>
	{+END}
</div>
