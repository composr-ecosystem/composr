{+START,IF_PASSED,CHILDREN}{+START,IF_PASSED,OTHER_IDS}{+START,IF,{$NEQ,{OTHER_IDS},}}
	{$SET,imploded_ids,{+START,IMPLODE,\,,OTHER_IDS}{+END}}
	<p class="post-show-more" data-tpl="postChildLoadLink" data-tpl-params="{+START,PARAMS_JSON,imploded_ids,ID}{_*}{+END}">
		<a class="js-click-threaded-load-more" data-click-pd="1" href="{$SELF_URL*,0,0,0,max_comments=200}">{+START,IF_NON_EMPTY,{CHILDREN}}{!SHOW_MORE_COMMENTS,{$INTEGER_FORMAT*,{$MIN,{NUM_TO_SHOW_LIMIT},{OTHER_IDS}}}}{+END}{+START,IF_EMPTY,{CHILDREN}}{!SHOW_COMMENTS,{$INTEGER_FORMAT*,{$MIN,{NUM_TO_SHOW_LIMIT},{OTHER_IDS}}}}{+END}</a>
	</p>
{+END}{+END}{+END}
