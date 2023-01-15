{+START,IF_NON_EMPTY,{CHILDREN}}{+START,IF,{$EQ,{CAT},root}}
	<hr class="spaced-rule" />

	<div class="boxless-space">
		{+START,BOX}{$BLOCK-,block=main_multi_content,param=gallery,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!GALLERIES}}}{+END}

		{+START,IF,{$CONFIG_OPTION,is_on_rating}}
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=gallery,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!GALLERIES}}}{+END}
		{+END}

		{+START,BOX}{$BLOCK-,block=main_multi_content,param=image,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!IMAGES}}}{+END}

		{+START,IF,{$CONFIG_OPTION,is_on_rating}}
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=image,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!IMAGES}}}{+END}
		{+END}

		{+START,BOX}{$BLOCK-,block=main_multi_content,param=video,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=recent,title={!RECENT,10,{!VIDEOS}}}{+END}

		{+START,IF,{$CONFIG_OPTION,is_on_rating}}
			{+START,BOX}{$BLOCK-,block=main_multi_content,param=video,render_mode=boxes,filter={CAT}*,no_links=1,efficient=0,give_context=0,include_breadcrumbs=1,render_if_empty=1,max=10,mode=top,title={!TOP,10,{!VIDEOS}}}{+END}
		{+END}
	</div>
{+END}{+END}
