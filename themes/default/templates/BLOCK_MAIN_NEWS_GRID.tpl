{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{SLIDER}

	{$SET,ajax_block_main_news_grid_wrapper,ajax-block-main-news-grid-wrapper-{$RAND%}}
	{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
	<div id="{$GET*,ajax_block_main_news_grid_wrapper}" class="block-main-news-grid" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
		<section>
			<div>
			{+START,IF,{$NOT,{BLOG}}}{+START,IF_NON_EMPTY,{TITLE}}
				<h2>{TITLE}</h2>
			{+END}{+END}

			{+START,IF_EMPTY,{SLIDER}{SUMMARY_CONTENT}{BRIEF_CONTENT}}
				<p class="nothing-here">{!NO_ENTRIES,news}</p>
			{+END}

			<div class="raw-ajax-grow-spot main-news-grid">
				{SUMMARY_CONTENT}
			</div>

			{+START,IF_NON_EMPTY,{BRIEF_CONTENT}}
				{+START,IF_NON_EMPTY,{SUMMARY_CONTENT}}
					<h3>{$?,{BLOG},{!BLOG_OLDER_NEWS},{!OLDER_NEWS}}</h3>
				{+END}

				{BRIEF_CONTENT}
			{+END}

			{+START,IF_PASSED,PAGINATION}
				{+START,IF_NON_EMPTY,{PAGINATION}}
					<div class="pagination-spacing clearfix ajax-block-wrapper-links">
						{PAGINATION}
					</div>
				{+END}
			{+END}

			{+START,IF_NON_EMPTY,{ARCHIVE_URL}{SUBMIT_URL}{RSS_URL}{ATOM_URL}}
				<ul class="horizontal-links associated-links-block-group">
					{+START,IF_NON_EMPTY,{ARCHIVE_URL}}
						<li><a rel="archives" href="{ARCHIVE_URL*}">{!VIEW_ARCHIVE}</a></li>
					{+END}
					{+START,IF_NON_EMPTY,{SUBMIT_URL}}
						<li><a rel="add" href="{SUBMIT_URL*}">{$?,{BLOG},{!ADD_NEWS_BLOG},{!ADD_NEWS}}</a></li>
					{+END}
					{+START,IF_NON_EMPTY,{RSS_URL}}
						<li><a href="{RSS_URL*}"><abbr title="Really Simple Syndication">RSS</abbr></a></li>
					{+END}
					{+START,IF_NON_EMPTY,{ATOM_URL}}
						<li><a href="{ATOM_URL*}">Atom</a></li>
					{+END}
				</ul>
			{+END}
			</div>
		</section>

		{+START,IF_PASSED,PAGINATION}
			{+START,IF_NON_EMPTY,{PAGINATION}}
				{+START,INCLUDE,AJAX_PAGINATION}
					WRAPPER_ID={$GET,ajax_block_main_news_grid_wrapper}
					ALLOW_INFINITE_SCROLL=1
				{+END}
			{+END}
		{+END}
	</div>
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{SUMMARY_CONTENT}
	{BRIEF_CONTENT}

	{PAGINATION}
{+END}
