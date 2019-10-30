{$SET,id,slider-{$REPLACE,_,-,{$REPLACE|,-,_,{BLOCK_ID}}}}
<div id="{$GET*,id}" class="block-main-hero-slider cms-slider {+START,IF,{$EQ,{EFFECT},slide}}cms-slider-slide{+END} {+START,IF,{$EQ,{EFFECT},fade}}cms-slider-fade{+END} {+START,IF,{FULLSCREEN}}cms-slider-fullscreen{+END} {$?,{HAS_MULTIPLE_ITEMS},has-multiple-items,has-single-item}" data-cms-slider="{ interval: {INTERVAL%} }">
	{+START,IF_PASSED_AND_TRUE,SHOW_INDICATORS}
		<ol class="cms-slider-indicators">
			{+START,LOOP,ITEMS}
				<li data-target="#{$GET*,id}" data-slide-to="{_loop_key*}"{+START,IF,{$EQ,{_loop_key},0}} class="active"{+END}></li>
			{+END}
		</ol>
	{+END}
	{+START,IF_PASSED_AND_TRUE,SHOW_SCROLL_DOWN}
		<a href="#!" class="cms-slider-scroll-button" style="display: none;">
			<div class="cms-slider-scroll-button-icon">
				{+START,INCLUDE,ICON}NAME=results/sortablefield_desc{+END}
			</div>
			<div class="cms-slider-scroll-button-caption">{!SCROLL_DOWN*}</div>
		</a>
	{+END}

	<div class="cms-slider-inner">
		{+START,LOOP,ITEMS}
			<div class="cms-slider-item{+START,IF,{$EQ,{_loop_key},0}} active{+END}" {+START,IF,{$EQ,{BACKGROUND_TYPE},image}}style="background-image: url('{BACKGROUND_URL*}');"{+END}>
				{+START,IF,{$EQ,{BACKGROUND_TYPE},video}}
					<img class="cms-slider-item-background {+START,IF,{$DESKTOP}}mobile-only{+END}" src="{BACKGROUND_THUMB_URL*}" alt="" />
					{+START,IF,{$DESKTOP}}
						<video class="cms-slider-item-background desktop-only" autoplay="autoplay" loop="loop" muted="muted">
							<source src="{BACKGROUND_URL*}" type="video/mp4" />
						</video>
					{+END}
				{+END}
				<div class="cms-slider-item-inner">
					<div class="container">
						{CONTENT_HTML}

						{+START,IF_PASSED,EDIT_URL}
							<a href="{EDIT_URL*}" class="cms-slider-item-edit-link">{+START,INCLUDE,ICON}NAME=admin/edit_this{+END} {!EDIT}</a>
						{+END}
					</div>
				</div>
			</div>
		{+END}
	</div>
	<a class="cms-slider-control-prev" href="#{$GET*,id}" role="button" data-slide="prev">
		<span class="cms-slider-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">{!PREVIOUS*}</span>
	</a>
	<a class="cms-slider-control-next" href="#{$GET*,id}" role="button" data-slide="next">
		<span class="cms-slider-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">{!NEXT*}</span>
	</a>
	<!-- Slider progress bar becomes visible when the "interval" parameter to [data-cms-slider] is set -->
	<div class="cms-slider-progress-bar">
		<div class="cms-slider-progress-bar-fill"></div>
	</div>
</div>
