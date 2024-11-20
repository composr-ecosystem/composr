<section class="box box---catalogue-default-grid-entry-wrap"><div class="box-inner">
	<h3><span class="name">{$TRUNCATE_LEFT,{FIELD_0},30,1,1,0,0.5}</span></h3>

	{+START,SET,TOOLTIP}
		{+START,IF_NON_EMPTY,{$TRIM,{FIELDS_GRID}}}
			<table class="map-table results-table">
				<tbody>
					{FIELDS_GRID}
				</tbody>
			</table>
		{+END}
	{+END}

	{$SET,displayed_thumb,0}

	{$SET,view_url,{$?,{$AND,{$MOBILE},{$IS_NON_EMPTY,{FIELDS_GRID}}},{$PAGE_LINK,_SELF:catalogues:entry:{ID}},{VIEW_URL}}}

	{+START,IF_PASSED,FIELD_1}
		{+START,IF_NON_EMPTY,{FIELD_1}}
			<div class="catalogue-entry-box-thumbnail">
				{+START,IF_NON_EMPTY,{$GET,view_url}}
					<a data-cms-tooltip="{ contents: '{$TRIM*;^,{$GET,TOOLTIP}}', width: '500px' }" href="{$GET*,view_url}"><img class="img-thumb" alt="{$STRIP_TAGS,{FIELD_0}}" src="{$THUMBNAIL*,{FIELD_1_PLAIN}}" /></a>
				{+END}

				{+START,IF_EMPTY,{$GET,view_url}}
					<span data-cms-tooltip="{ contents: '{$TRIM*;^,{$GET,TOOLTIP}}', width: '500px' }"><img class="img-thumb" alt="{$STRIP_TAGS,{FIELD_0}}" src="{$THUMBNAIL*,{FIELD_1_PLAIN}}" /></span>
				{+END}
			</div>

			{$SET,displayed_thumb,1}
		{+END}
	{+END}

	{+START,IF,{$NOT,{$GET,displayed_thumb}}}
		{+START,IF_NON_EMPTY,{$GET,view_url}}
			<p>
				<a data-cms-tooltip="{ contents: '{$TRIM*;^,{$GET,TOOLTIP}}', width: '500px' }" href="{$GET*,view_url}">{!VIEW}</a>
			</p>
		{+END}

		{+START,IF_EMPTY,{$GET,view_url}}
			{$GET,TOOLTIP}
		{+END}
	{+END}

	{+START,IF,{ALLOW_RATING}}
		{$SET-,rating,{$RATING,catalogue_entry__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_STATIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			<div class="ratings">{$GET,rating}</div>
		{+END}
	{+END}
</div></section>
