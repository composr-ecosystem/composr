<section class="box box---gallery-image-box"><div class="box-inner">
	{+START,IF_NON_EMPTY,{TITLE}}
		{+START,SET,content_box_title}
			{+START,IF,{GIVE_CONTEXT}}
				{!CONTENT_IS_OF_TYPE,{!IMAGE},{TITLE*}}
			{+END}

			{+START,IF,{$NOT,{GIVE_CONTEXT}}}
				{+START,FRACTIONAL_EDITABLE,{TITLE},title,_SEARCH:cms_galleries:__edit:{ID},0}{TITLE*}{+END}
			{+END}
		{+END}
		{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
			<h3>{+START,IF_NON_EMPTY,{URL}}<a class="subtle-link" href="{URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{URL}}</a>{+END}</h3>
		{+END}
	{+END}

	<div>
		<a href="{URL*}">{THUMB}</a>
	</div>

	{+START,IF_NON_EMPTY,{BREADCRUMBS}}
		<nav class="breadcrumbs" itemprop="breadcrumb"><p>{!LOCATED_IN,{BREADCRUMBS}}</p></nav>
	{+END}
</div></section>
