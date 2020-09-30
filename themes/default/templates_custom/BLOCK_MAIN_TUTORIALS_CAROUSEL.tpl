{$REQUIRE_JAVASCRIPT,core_rich_media}
{$REQUIRE_CSS,carousels}

{$SET-,carousel_id,{$RAND}}

<div class="xhtml-substr-no-break">
	<div id="carousel-{$GET*,carousel_id}" class="carousel carousel-{BLOCK_ID*}" style="display: none" data-view="Carousel" data-view-params="{+START,PARAMS_JSON,carousel_id}{_*}{+END}">
		<div class="move-left js-btn-car-move" data-move-amount="-50">{+START,INCLUDE,ICON}NAME=carousel/button_left{+END}</div>
		<div class="main"></div>
		<div class="move-right js-btn-car-move" data-move-amount="+50">{+START,INCLUDE,ICON}NAME=carousel/button_right{+END}</div>
	</div>

	<div class="carousel-temp" id="carousel-ns-{$GET*,carousel_id}">
		{+START,LOOP,TUTORIALS}
			<div style="display: inline-block">
				<a href="{URL*}" style="position: relative; display: inline-block;"><img src="{ICON*}" alt="" style="min-width: 130px;" /><span style="position: absolute; right: 0; bottom: 0; left: 0; padding: 0.3em 0.4em; color: white; background: rgba(0,0,0,0.6); font-size: 0.9em">{TITLE*}</span></a>
			</div>
		{+END}
	</div>
</div>
