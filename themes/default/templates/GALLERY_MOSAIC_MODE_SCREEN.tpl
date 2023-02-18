<div class="gallery-mode-screen gallery-mosaic-mode-screen" data-tpl="galleryMosaicModeScreen" itemscope="itemscope" itemtype="http://schema.org/ImageGallery">
	{TITLE}

	{+START,IF_NON_EMPTY,{$TRIM,{DESCRIPTION}}}
		<div class="gallery-description" itemprop="description">
			{$PARAGRAPH,{DESCRIPTION}}
		</div>
	{+END}

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,gallery,{CAT}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

	{$SET,children,{$BLOCK,block=main_multi_content,param=gallery,render_mode=boxes,pinned=,select={CAT}>,zone={$ZONE},sort={GALLERY_SORT*},max={$CONFIG_OPTION,subgallery_link_limit},no_links=1,pagination=1,give_context=0,include_breadcrumbs=0,render_if_empty=0,guid=module}}
	{$SET,entries,{$TRIM,{$BLOCK,block=main_gallery_mosaic,param={CAT_SELECT},zone={$ZONE},days={DAYS},max={$CONFIG_OPTION,gallery_entries_grid_per_page},show_sorting=1,pagination=1,select={IMAGE_SELECT},video_select={VIDEO_SELECT},filter={FILTER},video_filter={FILTER},block_id=module,render_if_empty=1}}}

	{+START,IF_NON_EMPTY,{$GET,children}}
		<h2 class="heading-subgalleries">{!SUBGALLERIES}</h2>

		{$GET,children}
	{+END}

	{+START,IF_NON_EMPTY,{$GET,children}}{+START,IF_NON_EMPTY,{$GET,entries}}
		<h2 class="heading-images-and-videos">{!IMAGES_AND_VIDEOS_IN,{_TITLE}}</h2>
	{+END}{+END}

	{+START,IF_NON_EMPTY,{$GET,entries}}
		{$GET,entries}
	{+END}

	{+START,IF_EMPTY,{$GET,entries}{$GET,children}}
		<p class="nothing-here">
			{!NO_ENTRIES}
		</p>
	{+END}

	{$REVIEW_STATUS,gallery,{CAT}}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{+START,INCLUDE,NOTIFICATION_BUTTONS}
		NOTIFICATIONS_TYPE=gallery_entry
		NOTIFICATIONS_ID={CAT}
		BREAK=1
		RIGHT=1
	{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={IMAGE_URL*}
		1_TITLE={!ADD_IMAGE}
		1_REL=add
		1_ICON=menu/cms/galleries/add_one_image
		2_URL={VIDEO_URL*}
		2_TITLE={!ADD_VIDEO}
		2_REL=add
		2_ICON=menu/cms/galleries/add_one_video
		3_URL={$?,{$OR,{$NOT,{$HAS_PRIVILEGE,may_download_gallery}},{$IS_EMPTY,{$GET,entries}}},,{$FIND_SCRIPT*,download_gallery}?cat={CAT*}{$KEEP*,0,1}}
		3_TITLE={!DOWNLOAD_GALLERY_CONTENTS}
		3_ICON=links/download_as_archive
		4_URL={ADD_GALLERY_URL*}
		4_TITLE={!ADD_GALLERY}
		4_REL=edit
		4_ICON=admin/add_one_category
		5_ACCESSKEY=q
		5_URL={EDIT_URL*}
		5_TITLE={!EDIT_THIS_GALLERY}
		5_REL=edit
		5_ICON=admin/edit_this_category
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			6_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=gallery:content_id={CAT}:redirect={$SELF_URL&}}
			6_TITLE={!report_content:REPORT_THIS}
			6_ICON=buttons/report
			6_REL=report
		{+END}
	{+END}

	<div class="clearfix lined-up-boxes">
		{+START,IF_NON_EMPTY,{MEMBER_DETAILS}}
			<div class="right">
				<div class="box box---gallery-member-details"><div class="box-inner">
					<h2>{_TITLE*}</h2>

					{MEMBER_DETAILS}
				</div></div>
			</div>
		{+END}
	</div>

	{+START,IF_NON_EMPTY,{RATING_DETAILS}}
		<div class="clearfix">
			<div class="ratings">
				{RATING_DETAILS}
			</div>
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{COMMENT_DETAILS}}
		<div class="content-screen-comments">
			{COMMENT_DETAILS}
		</div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

	{$,Uncomment the below if you want the root gallery to show recent and top content, then customise the GALLERY_POPULAR.tpl template to control specifics}
	{$,\{+START,INCLUDE,GALLERY_POPULAR\}\{+END\}}
</div>
