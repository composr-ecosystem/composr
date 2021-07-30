<div data-tpl="indexScreenFancierScreen" data-tpl-params="{+START,PARAMS_JSON,RAW_SEARCH_STRING}{_*}{+END}">
	{TITLE}

	{+START,IF_NON_EMPTY,{PRE}}
		<div class="index-screen-fancier-screen-pre" itemprop="description">
			{PRE}
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{CONTENT}}
		<div class="index-screen-fancier-screen-entries clearfix" itemprop="significantLinks">
			{+START,IF_PASSED_AND_TRUE,ARRAY}
				{+START,LOOP,CONTENT}
					{+START,IF_NON_EMPTY,{_loop_var}}
						{+START,IF_NON_EMPTY,{_loop_key}}
							<h2>{_loop_key*}</h2>
						{+END}

						<div{+START,IF,{$EQ,(CONTENT),1}} class="not-too-tall"{+END}>
							{_loop_var}
						</div>
					{+END}
				{+END}
				{+START,IF_EMPTY,{CONTENT}}
					<p class="nothing-here">
						{!NONE}
					</p>
				{+END}
			{+END}
			{+START,IF_NON_PASSED_OR_FALSE,ARRAY}
				{CONTENT}
			{+END}
		</div>
	{+END}
	{+START,IF_EMPTY,{CONTENT}}
		<p class="nothing-here">
			{!NO_ENTRIES}
		</p>
	{+END}

	{+START,IF_NON_EMPTY,{POST}}
		<div class="index-screen-fancier-screen-post">
			{POST}
		</div>
	{+END}

	{+START,IF_PASSED,PAGINATION}
		<div class="clearfix">
			{PAGINATION}
		</div>
	{+END}

	{+START,IF_PASSED,CATALOGUE}
		<hr class="spaced-rule" />

		{+START,SET,catalogue}{CATALOGUE}{+END}

		<div class="boxless-space">
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=catalogue_entry,render_mode=boxes,filter_b={$GET,catalogue}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!ENTRIES}}}{+END}

			{+START,IF,{$CONFIG_OPTION,is_on_rating}}
				{+START,BOX}{$BLOCK-,block=main_multi_content,param=catalogue_entry,render_mode=boxes,filter_b={$GET,catalogue}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!ENTRIES}}}{+END}
			{+END}
		</div>
	{+END}

	{+START,IF_PASSED,ADD_URL}
		{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
		{+START,INCLUDE,STAFF_ACTIONS}
			1_URL={ADD_URL*}
			1_TITLE={!ADD}
			1_REL=add
			1_ICON=admin/add
		{+END}
	{+END}
</div>
