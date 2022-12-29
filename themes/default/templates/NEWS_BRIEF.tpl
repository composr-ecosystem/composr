<div class="box box---news-brief"><div class="box-inner">
	<span class="right responsive float-separation">{DATE*}</span>

	<span><a href="{FULL_URL*}">{NEWS_TITLE}</a></span>
	{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT} <span class="comment-count">{$COMMENT_COUNT,news,{ID}}</span>{+END}
</div></div>
