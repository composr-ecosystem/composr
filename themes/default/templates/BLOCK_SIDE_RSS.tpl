{$REQUIRE_JAVASCRIPT,news}
{+START,IF,{$NOT,{TICKER}}}
	<section id="tray-{TITLE|}" data-toggleable-tray="{ save: true }" class="box box---block-side-rss">
		<div class="box-inner">
			<h3 class="toggleable-tray-title js-tray-header">
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
				{+END}</a>

				{+START,IF_NON_EMPTY,{TITLE}}
					<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{TITLE`}</a>
				{+END}
			</h3>

			<div class="toggleable-tray js-tray-content">
				{+START,IF_EMPTY,{CONTENT}}
					<p class="nothing-here">{!NO_NEWS}</p>
				{+END}
				{+START,IF_NON_EMPTY,{CONTENT}}
					<div class="webstandards-checker-off">
						{CONTENT}
					</div>
				{+END}
			</div>
		</div>
	</section>
{+END}

{+START,IF,{TICKER}}
	{$SET,side_news_id,{$RAND}}

	<section class="box box---block-side-rss"><div class="box-inner">
		{+START,IF_NON_EMPTY,{TITLE}}<h3>{TITLE`}</h3>{+END}

		{+START,IF_EMPTY,{CONTENT}}
			<p class="nothing-here">{!NO_NEWS}</p>
		{+END}
		{+START,IF_NON_EMPTY,{CONTENT}}
			<div class="webstandards-checker-off wide-ticker" id="news-scroller{$GET%,side_news_id}" data-cms-news-scroller="1">
				{CONTENT}
			</div>
		{+END}
	</div></section>
{+END}
