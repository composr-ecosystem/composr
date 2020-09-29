{+START,SET,TOOLTIP}
	<div class="gallery_tooltip">
		<table class="map_table results_table">
			{+START,IF,{$NOT,{$MOBILE}}}
				<colgroup>
					<col class="gallery_entry_field_name_column" />
					<col class="gallery_entry_field_value_column" />
				</colgroup>
			{+END}

			<tbody>
				<tr>
					<th class="de_th metadata_title">{!ADDED}</th>
					<td>{$DATE_AND_TIME*,1,0,0,{ADD_DATE_RAW}}</td>
				</tr>

				<tr>
					<th class="de_th metadata_title">{!BY}</th>
					<td><a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}">{$USERNAME*,{SUBMITTER},1}</a></td>
				</tr>

				{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
					<tr>
						<th class="de_th metadata_title">{!EDITED}</th>
						<td>{$DATE_AND_TIME*,1,0,0,{EDIT_DATE_RAW}}</td>
					</tr>
				{+END}

				{+START,IF,{$INLINE_STATS}}
					<tr>
						<th class="de_th metadata_title">{!COUNT_VIEWS}</th>
						<td>{VIEWS*}</td>
					</tr>
				{+END}

				{$SET-,rating,{$RATING,images,{ID},1,{SUBMITTER}}}
				{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
					<tr>
						<th class="de_th metadata_title">{!RATING}</th>
						<td>{$GET,rating}</td>
					</tr>
				{+END}
			</tbody>
		</table>
	</div>
{+END}

{+START,IF,{$EQ,{_GUID},carousel}}
	<a onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{$GET;^*,TOOLTIP}','auto',null,null,false,true);" href="{VIEW_URL*}"><img alt="{TITLE}" height="140" src="{$THUMBNAIL*,{THUMB_URL},140x140,website_specific,,,height}" /></a>
{+END}

{+START,IF,{$NEQ,{_GUID},carousel}}
	<div class="gallery_regular_thumb">
		<div class="img_thumb_wrap{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}} img_thumb_opaque{+END}" onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{$GET;^*,TOOLTIP}','auto',null,null,false,true);" onmouseleave="deactivate_tooltip(this);">
			{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}}
				{+START,INCLUDE,MASS_SELECT_MARKER}
					TYPE={MEDIA_TYPE}
					ID={ID}
				{+END}
			{+END}

			<a href="{VIEW_URL*}" class="leave_native_tooltip">{$TRIM,{THUMB}}</a>
		</div>

		<p class="gallery_media_title_cropped">
			{+START,FRACTIONAL_EDITABLE,{TITLE},title,_SEARCH:cms_galleries:__edit:{ID},1,1,{$HAS_EDIT_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries,galleries,{CAT}}}{$TRUNCATE_LEFT,{TITLE},23,0,0}{+END}
		</p>

		{+START,IF_PASSED,RATING_DETAILS}
			{$SET-,rating,{$RATING,images,{ID},{SUBMITTER},,,RATING_INLINE_STATIC}}
			{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
				<div class="grating">{$GET,rating}</div>
			{+END}
		{+END}
		<p class="gallery_regular_thumb_comments_count">
			{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT}
				<a href="{VIEW_URL*}">{$COMMENT_COUNT,images,{ID}}</a>
			{+END}
			{+START,IF_NON_PASSED_OR_FALSE,COMMENT_COUNT}
				<a href="{VIEW_URL*}">{!VIEW}</a>
			{+END}
		</p>
	</div>
{+END}
