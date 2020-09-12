{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__HEADER}{+END}

{$REQUIRE_CSS,skitter}
{$REQUIRE_JAVASCRIPT,skitter}
{$REQUIRE_JAVASCRIPT,image_slider}
{$SET,rand,{$RAND}}

{+START,IF_NON_EMPTY,{CONTENT}}
	<div class="box_skitter skitter" id="skitter-{$GET*,rand}" data-tpl="blockMainMultiContentSlider" data-tpl-params="{+START,PARAMS_JSON,rand,MILL}{_*}{+END}">
		<ul>
			{+START,LOOP,CONTENT_DATA}
				<li>
					<a href="#slider_{_loop_key*}"><img src="{$THUMBNAIL*,{CONTENT_IMAGE_URL},,,,,pad,both}" alt="" class="horizontal" /></a>
					<div class="label_text"><p>{CONTENT_TITLE_HTML}</p></div>
				</li>
			{+END}
		</ul>
	</div>
{+END}

{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__FOOTER}{+END}
