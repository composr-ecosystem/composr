<div class="news-archive-screen">
	{TITLE}

	<div class="news-archive-screen-content clearfix" itemprop="significantLinks">
		{$BLOCK,block=main_news,param={MAX},block_id=module,title=,select={SELECT},select_and={SELECT_AND},blogs={BLOGS},member_based={BLOG},zone={$ZONE},display_slides=9,display_summaries={$SUBTRACT,{MAX},9},display_briefs=0,no_links=1,pagination=1,attach_to_url_filter=1,filter={FILTER},render_mode=grid}
	</div>

	{+START,IF_PASSED,CAT}
		{$REVIEW_STATUS,news_category,{CAT}}

		{+START,INCLUDE,NOTIFICATION_BUTTONS}
			NOTIFICATIONS_TYPE=news_entry
			NOTIFICATIONS_ID={CAT}
			BREAK=1
			RIGHT=1
		{+END}
	{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={SUBMIT_URL*}
		1_TITLE={$?,{BLOG},{!ADD_NEWS_BLOG},{!ADD_NEWS}}
		1_REL=add
		1_ICON=admin/add
		2_URL={EDIT_CAT_URL*}
		2_TITLE={!EDIT_NEWS_CATEGORY}
		2_REL=edit
		2_ICON=admin/edit_this_category
	{+END}
</div>
