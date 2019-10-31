<section class="box box---block-bottom-latest-news">
	<div class="box-inner">
		<h3 class="box-heading">{TITLE}</h3>
		<div class="box-body">
			{+START,IF_EMPTY,{NEWS_ITEMS}}
				<p class="nothing-here">{$?,{BLOG},{!BLOG_NO_NEWS},{!NO_NEWS}}</p>
			{+END}
			{+START,IF_NON_EMPTY,{NEWS_ITEMS}}
				{+START,LOOP,NEWS_ITEMS}
					<a class="news-item" href="{FULL_URL*}">{+START,TRIM}
						<div class="news-item-thumb">
							<img src="{IMG_URL*}" alt="{$STRIP_TAGS*,{NEWS_TITLE}}" />
						</div>
						<div class="news-item-details">
							<h4 class="news-item-heading">{NEWS_TITLE*}</h4>
							<time class="news-item-date" datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{_DATE}}" itemprop="datePublished">{DATE*}</time>
						</div>
					{+END}</a>
				{+END}
			{+END}
		</div>
	</div>
</section>
