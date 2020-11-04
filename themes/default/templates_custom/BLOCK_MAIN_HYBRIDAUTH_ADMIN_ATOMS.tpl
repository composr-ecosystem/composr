<section class="box box---block-main-hybridauth-admin-atoms">
	<div class="box-inner">
		<h3>{PROVIDER*}: <a href="{FEED_PROFILE_URL*}" title="{FEED_DISPLAY_NAME*} {!LINK_NEW_WINDOW}" target="_blank">{FEED_DISPLAY_NAME*}</a></h3>

		{+START,LOOP,FEED}
			<div class="box box---news-box"><div class="box-inner">
				{+START,IF_PASSED,TITLE}
					<h3>{TITLE*}</h3>
				{+END}

				{+START,IF,{$DESKTOP}}
					{+START,IF_PASSED,IMAGE_URL}
						<div class="newscat-img newscat-img-author block-desktop">
							{+START,IF_PASSED,BEST_URL}
								<a href="{BEST_URL*}" title="{TITLE*} {!LINK_NEW_WINDOW}" target="_blank"><img width="100" height="100" src="{$THUMBNAIL*,{IMAGE_URL},100x100}" alt="" /></a>
							{+END}
							{+START,IF_NON_PASSED,BEST_URL}
								<img width="100" height="100" src="{$THUMBNAIL*,{IMAGE_URL},100x100}" alt="" />
							{+END}
						</div>
					{+END}
				{+END}

				<div class="meta-details" role="note">
					<ul class="meta-details-list">
						<li>{!POSTED_TIME_SIMPLE,{PUBLISHED*}}</li>
						{+START,IF_PASSED,AUTHOR_DISPLAY_NAME}
							<li>
								{+START,IF_PASSED,AUTHOR_PROFILE_URL}
									{!BY_SIMPLE,<a href="{AUTHOR_PROFILE_URL*}" title="{!AUTHOR}: {AUTHOR_DISPLAY_NAME*}">{AUTHOR_DISPLAY_NAME*}</a>}
								{+END}
								{+START,IF_NON_PASSED,AUTHOR_PROFILE_URL}
									{!BY_SIMPLE,{AUTHOR_DISPLAY_NAME*}}
								{+END}

								{+START,IF_PASSED,AUTHOR_PHOTO_URL}
									<img class="embedded-mini-avatar" src="{AUTHOR_PHOTO_URL*}" alt="" />
								{+END}
							</li>
						{+END}
					</ul>
				</div>

				<div>
					{+START,IF_PASSED,MESSAGE}
						{$PARAGRAPH,{$TRUNCATE_LEFT,{MESSAGE`},300,0,1}}
					{+END}
				</div>

				{+START,IF_PASSED,BEST_URL}
					<ul class="horizontal-links associated-links-block-group">
						<li><a href="{BEST_URL*}" title="{!READ_MORE}: {IDENTIFIER*} {!LINK_NEW_WINDOW}" target="_blank">{!READ_MORE}</a></li>
					</ul>
				{+END}
			</div></div>
		{+END}
	</div>
</section>
