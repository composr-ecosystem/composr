{$REQUIRE_JAVASCRIPT,news}

{$SET,RAND_FADER_NEWS,{$RAND}}
<section class="box box---block-main-image-fader-news" data-tpl="blockMainImageFaderNews" data-tpl-params="{+START,PARAMS_JSON,RAND_FADER_NEWS,NEWS,MILL}{_*}{+END}">
	<div class="box-inner">
		{+START,IF_NON_EMPTY,{TITLE}}
			<h2>{TITLE}</h2>
		{+END}

		<div class="image-fader-news-pic">
			<div class="img-thumb-wrap">
				<a id="image-fader-news-url-{$GET%,RAND_FADER_NEWS}" href="#!"><img id="image-fader-news-{$GET,RAND_FADER_NEWS}" src="{$IMG*,blank}" alt="" /></a>
			</div>
		</div>

		<div class="image-fader-news-html" id="image-fader-news-html-{$GET%,RAND_FADER_NEWS}">
			<span aria-busy="true"><img alt="" width="20" height="20" src="{$IMG*,loading}" /></span>
		</div>
	</div>
</section>

{+START,LOOP,NEWS}
	{+START,SET,news_html}
		{+START,IF_NON_EMPTY,{TITLE}}
			<h3><a href="{URL*`}">{TITLE`}</a></h3>
		{+END}

		<div class="meta-details" role="note">
			<ul class="meta-details-list">
				<li>{!POSTED_TIME_SIMPLE,{DATE*`}}</li>
				{+START,SET,author_details}
					{+START,IF_NON_EMPTY,{AUTHOR_URL}}
						{!BY_SIMPLE,<a href="{AUTHOR_URL*`}" title="{!AUTHOR`}: {AUTHOR*`}">{AUTHOR*`}</a>}
						{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={$AUTHOR_MEMBER,{AUTHOR}}{+END}
					{+END}

					{+START,IF_EMPTY,{AUTHOR_URL}}
						{+START,IF_NON_EMPTY,{$USERNAME*,{SUBMITTER}}}
							{!BY_SIMPLE,<a rel="author" href="{$MEMBER_PROFILE_URL*`,{SUBMITTER}}">{$USERNAME*`,{SUBMITTER}}</a>}
							{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
						{+END}
					{+END}
				{+END}

				{+START,IF_NON_EMPTY,{$GET,author_details}}
					<li>
						{$GET`,author_details}
					</li>
				{+END}
			</ul>
		</div>

		{BODY}

		{+START,IF,{$NEQ,{NEWS},1}}
			<div class="pagination">
				<nav class="clearfix">
					<a href="#!" rel="prev" accesskey="j" title="&laquo;&nbsp;{!PREVIOUS}: {!NEWS}" class="light js-click-btn-prev-cycle">&laquo;&nbsp;{!PREVIOUS}</a>
					<a href="#!" id="pause-button-{$GET,RAND_FADER_NEWS}" title="{!PAUSE}" class="light js-click-btn-pause-cycle">{!PAUSE}</a>
					<a href="#!" rel="next" accesskey="k" title="&laquo;&nbsp;{!NEXT}: {!NEWS}" class="light js-click-btn-next-cycle">{!NEXT}&nbsp;&raquo;</a>
				</nav>
			</div>
		{+END}
	{+END}
	<span id="image-fader-{$GET,RAND_FADER_NEWS}-news-item-{_loop_key}-html" data-tp-html="{ html: {$JSON_ENCODE*,{$GET,news_html}} }" style="display: none"></span>
{+END}
