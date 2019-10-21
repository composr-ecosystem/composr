<div class="gallery-mosaic-item is-image" {+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}}data-mass-selectable="1"{+END} data-focus-class="focus-within">
	{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}}
		{+START,INCLUDE,MASS_SELECT_MARKER}
			TYPE={MEDIA_TYPE}
			ID={ID}
		{+END}
	{+END}

	<a class="gallery-mosaic-item-inner img-thumb-opaque" href="{VIEW_URL*}">
		{$TRIM,{THUMB}}
		<div class="gallery-mosaic-item-overlay">
			<div class="gallery-mosaic-item-overlay-top">
				{+START,IF_PASSED,RATING_DETAILS}{+START,IF_NON_EMPTY,{$TRIM,{RATING_DETAILS}}}
					<div class="left grating">{RATING_DETAILS}</div>
				{+END}{+END}

				<div class="right">
					<span class="views">{+START,INCLUDE,ICON}NAME=cns_topic_modifiers/hot{+END} {VIEWS*} <span>{!VIEWS}</span></span>
					{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT}
						<span class="comments">{+START,INCLUDE,ICON}NAME=feedback/comment{+END} {$COMMENT_COUNT,images,{ID}}</span>
					{+END}
				</div>
			</div>

			<div class="gallery-mosaic-item-overlay-bottom">
				<div class="add-date">{$DATE_TIME,{ADD_DATE_RAW*}}</div>
				<h3 class="title">{TITLE}</h3>
				<div class="submitter">{+START,INCLUDE,ICON}NAME=content_types/member{+END} {$USERNAME*,{SUBMITTER},1}</div>
			</div>
		</div>
	</a>
</div>
