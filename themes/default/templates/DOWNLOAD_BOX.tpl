<div class="box box---download-box"><div class="box-inner">
	{+START,SET,content_box_title}
		{+START,IF,{GIVE_CONTEXT}}
			{!CONTENT_IS_OF_TYPE,{!DOWNLOAD},{NAME*}}
		{+END}

		{+START,IF,{$NOT,{GIVE_CONTEXT}}}
			{NAME*}
		{+END}
	{+END}
	{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
		<h3>{+START,IF_NON_EMPTY,{URL}}<a class="subtle-link" href="{URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{URL}}</a>{+END}</h3>
	{+END}

	<div class="meta-details" role="note">
		<dl class="meta-details-list">
			{+START,IF_NON_EMPTY,{AUTHOR}}
				<dt class="field-name">{!BY}:</dt> <dd>{AUTHOR*}</dd>
			{+END}
			{+START,IF,{$INLINE_STATS}}
				<dt class="field-name">{!COUNT_DOWNLOADS}:</dt> <dd>{DOWNLOADS*}</dd>
			{+END}
			<dt class="field-name">{!ADDED}:</dt> <dd>{DATE*}</dd>
			{+START,IF_PASSED,RATING}{+START,IF_NON_EMPTY,{RATING}}
				<dt class="field-name">{!RATING}:</dt> <dd>{RATING}</dd>
			{+END}{+END}
		</dl>
	</div>

	<div class="hide-if-in-panel">
		{+START,IF_NON_EMPTY,{IMGCODE}}
			<div class="download-box-pic"><a href="{URL*}">{IMGCODE}</a></div>
		{+END}

		<div class="download-box-description{+START,IF_NON_EMPTY,{IMGCODE}} pic{+END}">
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
		<ul class="horizontal-links associated-links-block-group">
			{+START,IF_PASSED,LICENCE}
				<li><a href="{URL*}">{!VIEW}</a></li>
			{+END}
			{+START,IF_NON_PASSED,LICENCE}
				<li><a href="{URL*}">{!MORE_INFO}</a></li>
				{+START,IF,{MAY_DOWNLOAD}}
					<li><a {+START,IF,{$NOT,{$INLINE_STATS}}} data-click-ga-track="{ category: '{!DOWNLOAD;^*}', action: '{ORIGINAL_FILENAME;^*}' }"{+END} title="{!DOWNLOAD_NOW}: {FILE_SIZE*}" href="{DOWNLOAD_URL*}">{!DOWNLOAD_NOW}</a></li>
				{+END}
			{+END}
		</ul>
	{+END}
</div></div>
