{$SET,no_comcode_page_edit_links,1}
{$INIT,done_includes,0}

{$,Needed to stop potential looping problems due to get_page_name() breaking context sensitivity. For example, if including one page that has a main_comcode_page_children block from the main_comcode_page_children block of another}

{+START,IF,{$NOT,{$GET,has_comcode_page_children_block,1}}}
	{$SET-,has_comcode_page_children_block,1}

	{+START,IF_ARRAY_NON_EMPTY,CHILDREN}
		<div data-tpl="blockMainComcodePageChildren">
			<h2>{!PAGES}</h2>
			{+START,LOOP,CHILDREN}
				<div class="box"><div class="box-inner clearfix">
					{$SET,PAGE,{$PREG_REPLACE,<h1[^<>]*>.*</h1>,,{$PREG_REPLACE,<figure[^<>]*>.*</figure>,,{$PREG_REPLACE,<div class="box staff-actions">.*</div>,,{$PREG_REPLACE,Comments.*,,{$LOAD_PAGE,{PAGE},{ZONE},1,1}},Us},Us},Us}}
					{$SET,IMAGE,{$?,{$IN_STR,{$GET,PAGE},<img},{$PREG_REPLACE,^.*(<img[^>]*>).*$,\1,{$GET,PAGE},sU},}}

					<h3>{TITLE*}</h3>

					{+START,IF_NON_EMPTY,{$GET,IMAGE}}
						<div class="right float-separation">
							{$PREG_REPLACE, class="[^"]*",,{$PREG_REPLACE,<img,<img width="100",{$PREG_REPLACE, width="\d+" height="\d+",,{$GET,IMAGE}}}}
						</div>
					{+END}

					{$STRIP_TAGS,{$TRUNCATE_LEFT,{$GET,PAGE},400,0,1},<div><p><em><i><b><strong><br>}

					<p>
						<a class="btn btn-primary btn-scri buttons--more" href="{$PAGE_LINK*,{ZONE}:{PAGE}}"><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
					</p>
				</div></div>
			{+END}
		</div>
	{+END}

	{$SET,done_includes,1}
{+END}
{$SET,no_comcode_page_edit_links,0}
