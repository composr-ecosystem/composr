{$SET,id,slider-{$REPLACE,_,-,{$REPLACE|,-,_,{BLOCK_ID}}}}
<div class="cms-slider-item{+START,IF,{$EQ,{ACTIVE},1}} active{+END}">
	<div class="cms-slider-item-inner {$?,{$EQ,{ITEMS_COUNT},1},has-1-news-item,has-{ITEMS_COUNT*}-news-items}">
		{+START,LOOP,NEWS_ITEMS}
			<div class="slide-news-item">
				<a href="{FULL_URL*}" class="slide-news-item-image-wrapper">
					<img src="{IMG_LARGE*}" alt="" class="slide-news-item-image">
				</a>
				<div class="slide-news-item-details">
					<a href="{CATEGORY_URL*}" class="slide-news-item-category btn btn-secondary">{CATEGORY*}</a>
					<a href="{FULL_URL*}" class="slide-news-item-details-inner">
						<h3 class="slide-news-item-heading">{NEWS_TITLE*}</h3>
						{+START,IF_NON_EMPTY,{SUMMARY}}
							<div class="slide-news-item-summary" style="display: none;">
								{+START,IF,{$AND,{$NOT,{$IN_STR,{SUMMARY},<p><div>}},{$NOT,{$IN_STR,{SUMMARY},<h}}}}<p class="news-summary-p">{+END}
								{+START,IF,{TRUNCATE}}{$TRUNCATE_LEFT,{SUMMARY},400,0,1,0,0.4}{+END}
								{+START,IF,{$NOT,{TRUNCATE}}}{SUMMARY}{+END}
								{+START,IF,{$AND,{$NOT,{$IN_STR,{SUMMARY},<p><div>}},{$NOT,{$IN_STR,{SUMMARY},<h}}}}</p>{+END}
							</div>
						{+END}
					</a>
				</div>
			</div>
		{+END}
	</div>
</div>
