<div class="box box___download_box"><div class="box_inner">
	{+START,SET,content_box_title}
		{+START,IF,{GIVE_CONTEXT}}
			{!CONTENT_IS_OF_TYPE,{!DOWNLOAD},{NAME*}}
		{+END}

		{+START,IF,{$NOT,{GIVE_CONTEXT}}}
			{+START,IF_NON_EMPTY,{ID}}
				<a href="{URL*}">{NAME*}</a>
			{+END}
			{+START,IF_EMPTY,{ID}}
				{NAME*}
			{+END}
		{+END}
	{+END}
	{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
		<h3>{$GET,content_box_title}</h3>
	{+END}

	<div class="meta_details" role="note">
		<dl class="meta_details_list">
			{+START,IF_NON_EMPTY,{AUTHOR}}
				<dt class="field_name">{!BY}:</dt> <dd>{AUTHOR*}</dd>
			{+END}
			{+START,IF,{$INLINE_STATS}}
				<dt class="field_name">{!COUNT_DOWNLOADS}:</dt> <dd>{DOWNLOADS*}</dd>
			{+END}
			<dt class="field_name">{!ADDED}:</dt> <dd>{DATE*}</dd>
			{+START,IF_PASSED,RATING}{+START,IF_NON_EMPTY,{RATING}}
				<dt class="field_name">{!RATING}:</dt> <dd>{RATING}</dd>
			{+END}{+END}
		</dl>
	</div>

	<div class="hide_if_in_panel">
		{+START,IF_NON_EMPTY,{IMGCODE}}
			<div class="download_box_pic"><a href="{URL*}">{$TRIM,{IMGCODE}}</a></div>
		{+END}

		<div class="download_box_description{+START,IF_NON_EMPTY,{IMGCODE}} pic{+END}">
			{+START,IF_PASSED,TEXT_SUMMARY}
				{$PARAGRAPH,{TEXT_SUMMARY*}}
			{+END}
			{+START,IF_NON_PASSED,TEXT_SUMMARY}
				{$PARAGRAPH,{$TRUNCATE_LEFT,{DESCRIPTION},460,0,1}}
			{+END}
		</div>

		{+START,IF_NON_EMPTY,{BREADCRUMBS}}
			<nav class="breadcrumbs" itemprop="breadcrumb"><p>{!LOCATED_IN,{BREADCRUMBS}}</p></nav>
		{+END}
	</div>

	{+START,IF_NON_EMPTY,{URL}}
		<ul class="horizontal_links associated_links_block_group">
			{+START,IF_PASSED,LICENCE}
				<li><a href="{URL*}">{!VIEW}</a></li>
			{+END}
			{+START,IF_NON_PASSED,LICENCE}
				<li><a href="{URL*}">{!MORE_INFO}</a></li>
				{+START,IF,{MAY_DOWNLOAD}}
					<li><a{+START,IF,{$NOT,{$INLINE_STATS}}} onclick="return ga_track(this,'{!DOWNLOAD;*}','{ORIGINAL_FILENAME;*}');"{+END} title="{!DOWNLOAD_NOW}: {FILE_SIZE*}" href="{DOWNLOAD_URL*}">{!DOWNLOAD_NOW}</a></li>
				{+END}
			{+END}
		</ul>
	{+END}
</div></div>
