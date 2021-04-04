{TITLE}

{+START,IF_NON_EMPTY,{STAFF}}
	<h2>{!STAFF}</h2>

	{STAFF}
{+END}

{+START,IF_NON_EMPTY,{RANKS}}
	<h2>{!RANK_SETS}</h2>

	{+START,LOOP,RANKS}
		{_loop_var}
	{+END}
{+END}

{+START,IF_NON_EMPTY,{OTHERS}}
	<h2>{$?,{$IS_EMPTY,{RANKS}},{!OTHER},{!OTHER_USERGROUPS}}</h2>

	{OTHERS}
{+END}

{+START,IF_NON_EMPTY,{CLUBS}}
	<h2>{!CLUBS}</h2>

	<p>
		{!CLUB_EXPLANATION}
	</p>

	{CLUBS}
{+END}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_cns_groups}}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={$PAGE_LINK*,_SEARCH:admin_cns_groups:add}
		1_TITLE={!ADD_GROUP}
		1_REL=add
		1_ICON=admin/add
	{+END}
{+END}
