<div class="clearfix rss-summary">
	<h3><a href="{FULL_URL_RAW*}">{$TRUNCATE_LEFT,{NEWS_TITLE`},70,1,1}</a></h3>

	{+START,IF_EMPTY,{CATEGORY_IMG}}
		{+START,IF_NON_EMPTY,{AUTHOR}}
			<div class="newscat-img newscat-img-author">
				<div class="news-by">{AUTHOR}</div>
			</div>
		{+END}
	{+END}
	{+START,IF_NON_EMPTY,{CATEGORY_IMG}}
		<div class="newscat-img newscat-img-author">
			<img width="100" height="100" src="{$ENSURE_PROTOCOL_SUITABILITY*,{CATEGORY_IMG}}" title="{CATEGORY`}" alt="{CATEGORY`}" />
			{CATEGORY`}
		</div>
	{+END}

	{+START,IF,{$OR,{$IS_NON_EMPTY,{DATE}},{$AND,{$NOT,{$IN_STR,{CATEGORY},<img}},{$IS_NON_EMPTY,{CATEGORY}}}}}
		<div class="meta-details" role="note">
			<ul class="meta-details-list">
				{+START,IF_NON_EMPTY,{DATE}}<li>{!POSTED_TIME_SIMPLE,{DATE*}}</li>{+END}
				{+START,IF,{$AND,{$NOT,{$IN_STR,{CATEGORY},<img}},{$IS_NON_EMPTY,{CATEGORY}}}}<li>{!IN,{CATEGORY}}</li>{+END}
			</ul>
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{NEWS}}
		{+START,IF,{$NOT,{$IN_STR,{NEWS},<p>}}}<p class="news-summary-p">{+END}{NEWS}{+START,IF,{$NOT,{$IN_STR,{NEWS},<p>}}}</p>{+END}
	{+END}

	{+START,COMMENT}
		<div data-toggleable-tray="{}">
			<div class="toggleable-tray-unstyled">
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!EXPAND}</a>
			</div>

			<div class="toggleable-tray-pulldown-spacer toggleable-tray js-tray-content" style="display: none" aria-expanded="false">
				{NEWS_FULL}
			</div>
		</div>
	{+END}
</div>

<ul class="horizontal-links associated-links-block-group">
	<li>{FULL_URL}</li>
</ul>
