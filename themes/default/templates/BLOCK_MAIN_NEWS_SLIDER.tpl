{$REQUIRE_JAVASCRIPT,news}
{$SET,id,slider-{$REPLACE,_,-,{$REPLACE|,-,_,{BLOCK_ID}}}}
<div id="{$GET*,id}" class="block-main-news-slider cms-slider cms-slider-slide" data-cms-slider="{ interval: {INTERVAL%}, disableIntervalOnMobile: true }" data-tpl="blockMainNewsSlider">
	{+START,IF_PASSED,SLIDES_COUNT_ARRAY}
		<ol class="cms-slider-indicators mobile-only">
			{+START,LOOP,SLIDES_COUNT_ARRAY}
				<li {+START,IF,{$EQ,{_loop_key},0}} class="active"{+END}><a href="#!" data-target="#{$GET*,id}" data-slide-to="{_loop_key*}">{$ADD*,{_loop_key},1}</a></li>
			{+END}
		</ol>
	{+END}

	<div class="cms-slider-inner">
		{SLIDES}
	</div>
	<a href="#{$GET*,id}" class="btn btn-secondary btn-prev-slide" data-slide="prev"><span class="chevron chevron-left"></span></a>
	<a href="#{$GET*,id}" class="btn btn-secondary btn-next-slide" data-slide="next"><span class="chevron chevron-right"></span></a>
	<div class="cms-slider-progress-bar">
		<div class="cms-slider-progress-bar-fill"></div>
	</div>
</div>
