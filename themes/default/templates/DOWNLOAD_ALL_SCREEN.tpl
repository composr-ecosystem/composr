{TITLE}

{+START,SET,SUB_CATEGORIES}
	{+START,LOOP,SUB_CATEGORIES}
		<h2>{LETTER*}</h2>

		{+START,IF_NON_EMPTY,{DOWNLOADS}}
			{DOWNLOADS}
		{+END}

		{+START,IF_EMPTY,{DOWNLOADS}}
			<p class="nothing-here">{!NO_ENTRIES,download}</p>
		{+END}
	{+END}
{+END}
{+START,IF_NON_EMPTY,{$TRIM,{$GET,SUB_CATEGORIES}}}
	{$GET,SUB_CATEGORIES}
{+END}
{+START,IF_EMPTY,{$TRIM,{$GET,SUB_CATEGORIES}}}
	<p class="nothing-here">{!NO_CATEGORIES,download_category}</p>
{+END}

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

<hr class="spaced-rule" />

<div class="boxless-space">
	{+START,IF_NON_PASSED,TEMPLATE_PREVIEW}
		{+START,BOX}{$BLOCK-,block=main_multi_content,param=download,render_mode=boxes,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!SECTION_DOWNLOADS}}}{+END}

		{+START,IF,{$CONFIG_OPTION,is_on_rating}}
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=download,render_mode=boxes,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!SECTION_DOWNLOADS}}}{+END}
		{+END}
	{+END}
	{+START,IF_PASSED,TEMPLATE_PREVIEW}
		<p>Lorem Ipsum</p>
	{+END}
</div>
