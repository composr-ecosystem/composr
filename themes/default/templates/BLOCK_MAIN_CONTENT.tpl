<section class="box box---main-content cguid-{_GUID|*}"><div class="box-inner">
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

	{+START,IF_NON_EMPTY,{SUBMIT_URL}{ARCHIVE_URL}}
		<ul class="horizontal-links associated-links-block-group force-margin">
			{+START,IF_NON_EMPTY,{SUBMIT_URL}}
				<li><a rel="add" href="{SUBMIT_URL*}">{ADD_NAME*}</a></li>
			{+END}
			{+START,IF_NON_EMPTY,{ARCHIVE_URL}}
				<li><a href="{ARCHIVE_URL*}" title="{!ARCHIVES}: {TYPE*}">{!ARCHIVES}</a></li>
			{+END}
		</ul>
	{+END}
</div></section>
