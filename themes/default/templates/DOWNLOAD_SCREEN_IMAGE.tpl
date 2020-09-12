{$SET,FIRST_IMAGE_ID,{ID}}

<figure>
	<div>
		<a rel="lightbox" href="{IMAGE_URL*}"><img class="img-thumb" alt="{TITLE*}" src="{$THUMBNAIL*,{IMAGE_URL}}" /></a>
	</div>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated-details">
			{DESCRIPTION}
		</figcaption>
	{+END}

	{+START,IF_NON_EMPTY,{EDIT_URL}}
		<p class="associated-link associated-links-block-group">
			<a href="{EDIT_URL*}" title="{!EDIT_IMAGE}, #{ID*}">{!EDIT_LINK}</a>
		</p>
	{+END}
</figure>
