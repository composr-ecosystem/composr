<div class="glide__slide carousel-mode-thumb">
	<a href="{VIEW_URL*}">{$TRIM,{THUMB}}</a>

	{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries}}
		{+START,INCLUDE,MASS_SELECT_MARKER}
			TYPE={TYPE}
			ID={ID}
		{+END}
	{+END}
</div>
