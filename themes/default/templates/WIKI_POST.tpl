{$REQUIRE_JAVASCRIPT,wiki}

<div data-tpl="wikiPost" data-tpl-params="{+START,PARAMS_JSON,ID}{_*}{+END}">
	<div>
		<div class="cns-forum-box-left cns-forum-box-right cns-post-details" role="note">
			<a id="post-{ID*}"></a>

			<div class="wiki-topic-poster-name">
				{+START,IF_NON_EMPTY,{POSTER_URL}}
					{!CONTENT_BY,<a href="{POSTER_URL*}">{$DISPLAYED_USERNAME*,{POSTER}}</a>}
				{+END}
				{+START,IF_EMPTY,{POSTER_URL}}
					{!CONTENT_BY,{POSTER*}}
				{+END}
			</div>

			<div class="cns-post-details-date">
				{!POSTED_TIME_SIMPLE,<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{POST_DATE_RAW}}">{POST_DATE*}</time>}
			</div>

			{+START,IF_NON_EMPTY,{UNVALIDATED}}
				<div class="cns-post-details-unvalidated">
					{UNVALIDATED*}
				</div>
			{+END}
		</div>
	</div>
	<div>
		<div class="cns-topic-post-area cns-post-main-column wiki-topic-post-area" id="pe-{ID*}">
			{POST}
			{$METADATA_IMAGE_EXTRACT,{POST}}

			{$REVIEW_STATUS,wiki_post,{ID}}
		</div>
	</div>
	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,wiki_post,{ID}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry},1}{+END}
	{+START,IF_NON_EMPTY,{BUTTONS}}
		<div>
			<div class="cns-left-post-buttons post-buttons cns-post-main-column">
				<div class="buttons-group post-buttons wiki-post-buttons">
					<div class="buttons-group-inner">
						{BUTTONS}

						{+START,INCLUDE,BUTTON_SCREEN_ITEM}
							{+START,IF,{$ADDON_INSTALLED,tickets}}
								URL={$PAGE_LINK,_SEARCH:report_content:content_type=wiki_post:content_id={ID}:redirect={$SELF_URL&}}
								TITLE={!report_content:REPORT_THIS}
								FULL_TITLE={!report_content:REPORT_THIS}
								IMG=buttons/report
								IMMEDIATE=0
								REL=report
							{+END}
						{+END}

						{+START,IF,{STAFF_ACCESS}}
							<div id="cell-mark-{ID*}" class="cns-off mass-select-marker wiki-mass-select-marker">
								<form title="{!MARKER}: {ID*}" method="post" action="#" id="form-mark-{ID*}">
									<div>
										{+START,IF,{$NOT,{$IS_GUEST}}}<label for="mark_{ID*}" class="accessibility-hidden">{!MARKER} #{ID*}:</label>{+END}{$,Guests don't see this so search engines don't; hopefully people with screen-readers are logged in}
										<input class="js-click-checkbox-set-cell-mark-class js-click-show-wiki-merge-button"{+START,IF,{$NOT,{$IS_GUEST}}} title="{!MARKER} #{ID*}"{+END} value="1" type="checkbox" id="mark_{ID*}" name="mark_{ID*}" />
									</div>
								</form>
							</div>
						{+END}
					</div>
				</div>

				{+START,SET,commented_out}
					{+START,IF,{$EQ,{$CONFIG_OPTION,is_on_rating},1}}
						<div class="wiki-post-below">
							<form title="{!RATING}" class="inline" action="{RATE_URL*}" method="post">
								{$INSERT_FORM_POST_SECURITY}

								{RATING}
							</form>
						</div>
					{+END}
				{+END}
			</div>
		</div>
	{+END}
</div>
