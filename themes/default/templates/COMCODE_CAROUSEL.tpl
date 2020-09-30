{$REQUIRE_JAVASCRIPT,core_rich_media}
{$REQUIRE_CSS,carousels}

{$SET-,carousel_id,{$RAND}}

<div class="xhtml-substr-no-break">
	<div id="carousel-{$GET*,carousel_id}" class="carousel" style="display: none" data-view="Carousel" data-view-params="{+START,PARAMS_JSON,carousel_id}{_*}{+END}">
		<div class="move-left js-btn-car-move" data-move-amount="-{SCROLL_AMOUNT%}">{+START,INCLUDE,ICON}NAME=carousel/button_left{+END}</div>
		<div class="main"></div>
		<div class="move-right js-btn-car-move" data-move-amount="+{SCROLL_AMOUNT%}">{+START,INCLUDE,ICON}NAME=carousel/button_right{+END}</div>
	</div>

	<div class="carousel-temp" id="carousel-ns-{$GET*,carousel_id}">
		{CONTENT}
	</div>
</div>
