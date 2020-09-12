{$SET,id,slider-{$REPLACE,_,-,{$REPLACE|,-,_,{BLOCK_ID}}}}
<div class="cms-slider-item{+START,IF,{$EQ,{ACTIVE},1}} active{+END}">
	<div class="cms-slider-item-inner {$?,{$EQ,{NEWS_ITEMS},1},has-1-news-item,has-{NEWS_ITEMS*}-news-items}">
		{+START,LOOP,NEWS_ITEMS}
			{+START,IF_NON_EMPTY,{REP_IMAGE_URL}}
				<div class="slide-news-item">
					<a href="{FULL_URL*}" class="slide-news-item-image-wrapper"><img src="{REP_IMAGE_URL*}" alt="" class="slide-news-item-image" /></a>
					<div class="slide-news-item-details">
						<a href="{CATEGORY_URL*}" class="slide-news-item-category btn btn-secondary">{CATEGORY*}</a>
						<a href="{FULL_URL*}" class="slide-news-item-details-inner">{+START,TRIM}
							<h3 class="slide-news-item-heading">{NEWS_TITLE*}</h3>
							{+START,IF_NON_EMPTY,{NEWS}}
								<div class="slide-news-item-summary" style="display: none;">
									{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p><div>}},{$NOT,{$IN_STR,{NEWS},<h}}}}<p class="news-summary-p">{+END}
									{+START,IF,{TRUNCATE}}{$TRUNCATE_LEFT,{NEWS},400,0,1,0,0.4}{+END}
									{+START,IF,{$NOT,{TRUNCATE}}}{NEWS}{+END}
									{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p><div>}},{$NOT,{$IN_STR,{NEWS},<h}}}}</p>{+END}
								</div>
							{+END}
						{+END}</a>
					</div>
				</div>
			{+END}
		{+END}
	</div>
</div>
