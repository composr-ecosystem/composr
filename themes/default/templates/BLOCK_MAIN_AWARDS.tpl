<section class="box box---main-awards cguid-{_GUID|*}"><div class="box-inner">
	{+START,NO_PREPROCESSING}
		{$SET,content_box_title,}
		{$SET,skip_content_box_title,1}
		{$SET,eval_content,{CONTENT}}{$,Force early evaluation, to get title}
		{+START,IF_NON_EMPTY,{TITLE}}
			<h2>
				{TITLE*}{+START,IF_NON_EMPTY,{$GET,content_box_title}}: {$GET,content_box_title}{+END}
			</h2>
		{+END}
		{$SET,skip_content_box_title,0}
	{+END}

	{$PREG_REPLACE,^\s*<section class="box [^"]+"><div class="box-inner">(.*)</div></section>\s*$,$1,{$GET,eval_content}}

	{+START,IF_NON_EMPTY,{AWARDEE_USERNAME}}
		<p class="additional-details">
			{!AWARDED_TO,<a href="{AWARDEE_PROFILE_URL*}">{$DISPLAYED_USERNAME*,{AWARDEE_USERNAME}}</a>}
		</p>
	{+END}

	<ul class="horizontal-links associated-links-block-group force-margin">
		{+START,IF_NON_EMPTY,{SUBMIT_URL}}
			<li><a rel="add" href="{SUBMIT_URL*}">{ADD_NAME*}</a></li>
		{+END}
		<li><a href="{ARCHIVE_URL*}" title="{!ARCHIVES}: {TYPE*}">{!ARCHIVES}</a></li>
	</ul>
</div></section>
