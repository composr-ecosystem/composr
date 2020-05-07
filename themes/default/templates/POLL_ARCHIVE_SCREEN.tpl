{TITLE}

{$BLOCK,block=main_multi_content,param=poll,render_mode=boxes,pinned=,zone={$ZONE},sort=recent,max=20,no_links=1,pagination=1,give_context=0,include_breadcrumbs=0,block_id=module,guid=module}

{+START,INCLUDE,STAFF_ACTIONS}
	{+START,IF_PASSED,ADD_URL}
		1_URL={ADD_URL*}
		1_TITLE={!ADD}
		1_REL=add
		1_ICON=admin/add
	{+END}
{+END}
