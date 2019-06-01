{$REQUIRE_JAVASCRIPT,core_rich_media}
{$REQUIRE_JAVASCRIPT,core_notifications}
{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,galleries}
{$REQUIRE_JAVASCRIPT,mediaelement-and-player}

<div class="gallery-nav" data-view="GalleryNav" data-view-params="{+START,PARAMS_JSON,_X,_N}{_*}{+END}">
	<div class="gallery-nav-inner">
		{$,Back}
		{+START,IF_NON_EMPTY,{BACK_URL}}
		<a class="gallery-nav-prev-btn" rel="prev" accesskey="j" href="{BACK_URL*}">
			{+START,INCLUDE,ICON}NAME=buttons/previous{+END} 
			<span>{!PREVIOUS}</span>
			{BACK_THUMB}
		</a>
		{+END}

		<div class="gallery-nav-status">
			<span>
				<span class="must-show-together">{!GALLERY_MOBILE_PAGE_OF,{X*},{N*}}</span>
			</span>
		</div>

		{$,Start slideshow}
		{+START,IF_NON_EMPTY,{SLIDESHOW_URL}}{+START,IF,{$DESKTOP}}
			<a class="btn btn-primary btn-scr buttons--slideshow desktop-only" rel="nofollow" {+START,IF,{$DESKTOP}}title="{!LINK_NEW_WINDOW*}" target="_blank"{+END} href="{SLIDESHOW_URL*}" data-link-start-slideshow="{}"><span>{+START,INCLUDE,ICON}NAME=buttons/slideshow{+END} {!_SLIDESHOW}</span></a>
		{+END}{+END}

		{$,Next}
		{+START,IF_NON_EMPTY,{NEXT_URL}}
		<a class="gallery-nav-next-btn" rel="next" accesskey="k" href="{NEXT_URL*}">
			{NEXT_THUMB}
			<span>{!NEXT}</span> 
			{+START,INCLUDE,ICON}NAME=buttons/next{+END}
		</a>
		{+END}
	</div>

	{$,Different positioning of slideshow button for mobiles, due to limited space}
	{+START,IF_NON_EMPTY,{SLIDESHOW_URL}}
		<div class="clearfix mobile-only">
			<div class="right">
				<a class="btn btn-primary btn-scr buttons--slideshow" rel="nofollow" {+START,IF,{$DESKTOP}}title="{!LINK_NEW_WINDOW*}" target="_blank"{+END} href="{SLIDESHOW_URL*}" data-link-start-slideshow="{}"><span>{+START,INCLUDE,ICON}NAME=buttons/slideshow{+END} {!_SLIDESHOW}</span></a>
			</div>
		</div>
	{+END}
</div>
