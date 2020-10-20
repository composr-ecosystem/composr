<div class="downloads-block">
	<h2>{NAME*} by {AUTHOR*}</h2>

	<div class="downloads-block-content">
		{+START,IF,{$OR,{$NOT,{$IN_STR,{$BREADCRUMBS},Composr Releases}},{$IS_NON_EMPTY,{FULL_IMG_URL}}}}
			<div class="downloads-block-img">
				<a href="{URL*}"><img alt="" width="200" height="154" src="{$THUMBNAIL*,{$?,{$IS_EMPTY,{FULL_IMG_URL}},{$IMG*,composr_homesite/downloads/no-image},{FULL_IMG_URL}},200x154,,,,pad,both,#FFFFFF}" /></a>
			</div>
		{+END}

		<div class="downloads-block-txt">
			<div class="p_like">
				{$PARAGRAPH,{$TRUNCATE_LEFT,{DESCRIPTION},400,0,1}}
			</div>

			<div class="downloads-block-strip">
				{+START,IF,{$INLINE_STATS}}
					<div class="downloads-strip-blk1">
						Downloads: {DOWNLOADS*}
					</div>
				{+END}

				<div class="downloads-strip-blk2">
					Added: {DATE*}
				</div>

				{+START,IF_PASSED,RATING}
					<div class="downloads-strip-blk2">
						Rating: {RATING}
					</div>
				{+END}
			</div>

			<div class="downloads-block-more">
				<a href="{URL*}">More info</a>
			</div>

			{+START,IF,{MAY_DOWNLOAD}}
				<div class="downloads-block-download">
					<a{+START,INCLUDE,_DOWNLOAD_BUTTON}{+END} href="{$FIND_SCRIPT*,dload}?id={ID*}{$KEEP*}{+START,IF,{$EQ,{$CONFIG_OPTION,anti_leech},1}}&amp;for_session={$SESSION_HASHED*}{+END}">Download now ({FILE_SIZE*})</a>
				</div>
			{+END}
		</div>
	</div>
</div>
