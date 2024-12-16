{$REQUIRE_CSS,achievements}

<div class="achievement-progress-wrapper" title="{PROGRESS_TITLE*}">
	<div class="achievement-progress{+START,IF_NON_EMPTY,{ADDITIONAL_CLASSES}} {ADDITIONAL_CLASSES*}{+END}" style="width: {PROGRESS*}%"></div>
</div>
