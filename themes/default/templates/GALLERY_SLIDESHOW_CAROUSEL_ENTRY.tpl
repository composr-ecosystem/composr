<div class="glide__slide slideshow-carousel-entry is-{TYPE*}{+START,IF,{IS_CURRENT}} is-current{+END}"
	data-vw-id="{ID*}" data-vw-cat="{CAT*}" data-vw-full-url="{$ENSURE_PROTOCOL_SUITABILITY*,{FULL_URL}}"
	{+START,IF_PASSED,COMMENTS_OPTIONS} data-vw-comments-options="{COMMENTS_OPTIONS*}" data-vw-comments-options-hash="{COMMENTS_OPTIONS_HASH*}"{+END}>
	<a class="slideshow-carousel-entry-link" href="{VIEW_URL_2*}">{$TRIM,{THUMB}}</a>

	<div class="slideshow-details-overlay" hidden="hidden">
		{+START,IF_NON_EMPTY,{_TITLE}}
			<div class="slideshow-details-overlay-top">
				<h1 class="title"><a href="{VIEW_URL_2*}">{_TITLE*}</a></h1>
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{DESCRIPTION}}
			<div class="slideshow-details-overlay-bottom">
				<div class="description">
					{$PARAGRAPH,{DESCRIPTION}}
				</div>
			</div>
		{+END}
	</div>
</div>
