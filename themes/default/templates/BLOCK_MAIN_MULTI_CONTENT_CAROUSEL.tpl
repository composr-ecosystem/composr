{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__HEADER}{+END}

	{$REQUIRE_JAVASCRIPT,core_rich_media}
	{$REQUIRE_CSS,carousels}

	{$SET-,carousel_id,{$RAND}}

	{+START,IF_NON_EMPTY,{CONTENT}}
		<div id="carousel-{$GET*,carousel_id}" class="carousel" style="display: none" data-view="Carousel" data-view-params="{+START,PARAMS_JSON,carousel_id}{_*}{+END}">
			<div class="move-left js-btn-car-move" data-move-amount="-30">{+START,INCLUDE,ICON}NAME=carousel/button_left{+END}</div>
			<div class="main raw-ajax-grow-spot"></div>
			<div class="move-right js-btn-car-move" data-move-amount="+30">{+START,INCLUDE,ICON}NAME=carousel/button_right{+END}</div>
		</div>

		<div class="carousel-temp" id="carousel-ns-{$GET*,carousel_id}">
			{+START,LOOP,CONTENT_DATA}
				<a href="{CONTENT_URL*}"><img class="img-thumb" alt="{CONTENT_TITLE_PLAIN*}" src="{$THUMBNAIL*,{CONTENT_IMAGE_URL}}" /></a>
			{+END}
		</div>
	{+END}

	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__FOOTER}{+END}
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,LOOP,CONTENT}
		{_loop_var}
	{+END}
{+END}
