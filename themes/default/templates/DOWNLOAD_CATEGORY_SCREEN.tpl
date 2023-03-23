{TITLE}

{WARNING_DETAILS}

{+START,IF_NON_EMPTY,{DESCRIPTION}}
	<div itemprop="description">
		{$PARAGRAPH,{DESCRIPTION}}
	</div>
{+END}

{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,download_category,{ID}}}
{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

{$SET,subcategories,{$BLOCK,block=main_multi_content,param=download_category,render_mode=boxes,pinned=,select={ID}>,efficient=0,zone={$ZONE},sort=,title=,max={$CONFIG_OPTION,download_subcats_per_page},no_links=1,pagination=1,give_context=0,include_breadcrumbs=0,render_if_empty=0,guid=module}}
{+START,IF_NON_EMPTY,{$GET,subcategories}}
	<div class="box box---download-category-screen"><div class="box-inner compacted-subbox-stream">
		<h2>{$?,{$EQ,{ID},1},{!CATEGORIES},{!SUBCATEGORIES_HERE}}</h2>

		<div>
			{$GET,subcategories}
		</div>
	</div></div>
{+END}

{$BLOCK,block=main_multi_content,param=download,render_mode=boxes,pinned=,select={SELECT},efficient=0=zone={$ZONE},sort={SORT},max={$CONFIG_OPTION,download_entries_per_page},no_links=1,pagination=1,give_context=0,include_breadcrumbs=0,attach_to_url_filter=1,filter={FILTER},block_id=module,guid=module}

<div class="right">
	{+START,INCLUDE,NOTIFICATION_BUTTONS}
		NOTIFICATIONS_TYPE=download
		NOTIFICATIONS_ID={ID}
	{+END}
</div>

<div class="box category-sorter inline-block"><div class="box-inner">
	{$SET,show_sort_button,1}
	{SORTING}
</div></div>

{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

{$REVIEW_STATUS,download_category,{ID}}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={SUBMIT_URL*}
	1_TITLE={!ADD_DOWNLOAD}
	1_REL=add
	1_ICON=admin/add
	2_URL={ADD_CAT_URL*}
	2_TITLE={!ADD_DOWNLOAD_CATEGORY}
	2_REL=add
	2_ICON=admin/add_one_category
	3_ACCESSKEY=q
	3_URL={EDIT_CAT_URL*}
	3_TITLE={!EDIT_DOWNLOAD_CATEGORY}
	3_REL=edit
	3_ICON=admin/edit_this_category
{+END}

{+START,IF_NON_EMPTY,{$GET,subcategories}}{+START,IF,{$EQ,{ID},{$DB_FIRST_ID}}}{+START,IF,{$NOT,{$CONFIG_OPTION,downloads_subcat_narrowin}}}
	<hr class="spaced-rule" />

	<div class="boxless-space">
		{+START,BOX}{$BLOCK-,block=main_multi_content,param=download,render_mode=boxes,filter={ID}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!SECTION_DOWNLOADS}}}{+END}

		{+START,IF,{$CONFIG_OPTION,is_on_rating}}
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=download,render_mode=boxes,filter={ID}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!SECTION_DOWNLOADS}}}{+END}
		{+END}
	</div>
{+END}{+END}{+END}
