<div class="glide__slide carousel-mode-thumb">
	<a href="{VIEW_URL*}"><img class="img-thumb" alt="{_TITLE*}" src="{$THUMBNAIL*,{IMAGE_URL}}" /></a>

	{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}}
		{+START,INCLUDE,MASS_SELECT_MARKER}
			TYPE={TYPE}
			ID={ID}
		{+END}
	{+END}
</div>
