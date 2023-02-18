{TITLE}

{$REQUIRE_CSS,shopping}

{+START,IF_NON_EMPTY,{DESCRIPTION}}
	<div class="box box---catalogue-products-category-screen--description"><div class="box-inner">
		<div itemprop="description">
			{$PARAGRAPH,{DESCRIPTION}}
		</div>
	</div></div>
{+END}

{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,catalogue_category,{ID}}}
{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

{$SET,subcategories,{$BLOCK,block=main_multi_content,param=catalogue_category,pinned=,render_mode=boxes,select={ID}>,zone={$ZONE},sort={CC_SORT},max={$CONFIG_OPTION,catalogue_subcats_per_page},no_links=1,pagination=1,give_context=0,include_breadcrumbs=0,attach_to_url_filter=1,render_if_empty=0,guid=module}}
{+START,IF_NON_EMPTY,{$GET,subcategories}}
	<div class="box box---catalogue-category-screen"><div class="box-inner compacted-subbox-stream">
		<h2>{!SUBCATEGORIES_HERE}</h2>

		<div>
			{$GET,subcategories}
		</div>
	</div></div>
{+END}

{$BLOCK,block=main_cc_embed,param={ID},select={CAT_SELECT},zone={$ZONE},max={$CONFIG_OPTION,catalogue_entries_per_page},pagination=1,sorting=1,filter={FILTER},block_id=module}

{+START,IF_PASSED,ENTRIES}
	{+START,IF,{$IN_STR,{ENTRIES},<img}}
		<p class="vertical-alignment">
			{+START,INCLUDE,ICON}
				NAME=help
				ICON_SIZE=24
			{+END}
			<span>{!HOVER_FOR_FULL}</span>
		</p>
	{+END}
{+END}

{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

{$REVIEW_STATUS,catalogue_category,{ID}}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={ADD_ENTRY_URL*}
	1_TITLE={!do_next:NEXT_ITEM_add}
	1_REL=add
	1_ICON=admin/add
	2_URL={ADD_CAT_URL*}
	2_TITLE={!do_next:NEXT_ITEM_add_one_category}
	2_REL=add
	2_ICON=admin/add_one_category
	3_ACCESSKEY=q
	3_URL={EDIT_CAT_URL*}
	3_TITLE={!do_next:NEXT_ITEM_edit_this_category}
	3_REL=edit
	3_ICON=admin/edit_this_category
	4_URL={EDIT_CATALOGUE_URL*}
	4_TITLE={!EDIT_THIS_CATALOGUE}
	4_ICON=menu/cms/catalogues/edit_this_catalogue
{+END}
