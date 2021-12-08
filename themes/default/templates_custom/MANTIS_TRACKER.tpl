{$REQUIRE_JAVASCRIPT,composr_homesite_support_credits}

{+START,IF_EMPTY,{ISSUES}}
	<p class="nothing-here">
		{!FEATURES_NOTHING_YET}
	</p>
{+END}

{+START,IF_NON_EMPTY,{ISSUES}}
	<div class="tracker-issues" data-tpl="mantisTracker">
		{+START,LOOP,ISSUES}
			<div class="box"><div class="box-inner">
				<h3>{CATEGORY*}: {SUMMARY*}</h3>

				<div class="tracker-issue-a">
					<p class="tracker-issue-votes">
						<strong>{!FEATURES_VOTES_lc,{VOTES*}}</strong>
					</p>

					{+START,IF,{VOTED}}
						<p class="js-click-add-voted-class tracker-issue-voting-status tracker-issue-not-voted">
							<a target="_blank" href="{UNVOTE_URL*}" title="{!FEATURES_UNVOTE} {!LINK_NEW_WINDOW}"><img width="16" height="16" src="{$IMG*,icons/tracker/minus}" /> <span>{!FEATURES_UNVOTE}</span></a>
						</p>
					{+END}

					{+START,IF,{$NOT,{VOTED}}}
						<p class="tracker-issue-voting-status tracker-issue-voted">
							<a target="_blank" href="{VOTE_URL*}" title="{!FEATURES_VOTE} {!LINK_NEW_WINDOW}"><img width="16" height="16" src="{$IMG*,icons/tracker/plus}" /> <span>{!FEATURES_VOTE}</span></a>
						</p>
					{+END}

					<p class="tracker-issue-progress">
						{!FEATURES_RAISED_PERCENT_OF,{PERCENTAGE*},{CREDITS*}}

						{+START,IF_PASSED,COST}
							<br />
							<span class="associated-details">({!FEATURES_CREDITS_HOURS_COST,{CREDITS*},{HOURS*},{$TRIM,{COST}}})</span>
						{+END}
					</p>
				</div>

				<div class="tracker-issue-b">
					<p class="tracker-issue-description">
						{$TRUNCATE_LEFT,{DESCRIPTION},310,1,1}
					</p>

					<p class="associated-details tracker-issue-poster">
						{!FEATURES_SUGGESTED_BY,{MEMBER_LINK},{DATE*}}
					</p>

					<p class="associated-link-to-small tracker-issue-link">
						&raquo; <a href="{FULL_URL*}">{!FEATURES_FULL_DETAILS}</a> ({!FEATURES_COMMENTS_lc,{NUM_COMMENTS*}})
					</p>
				</div>
			</div></div>
		{+END}
	</div>
{+END}

{+START,IF_NON_EMPTY,{PAGINATION}}
	<div class="clearfix">
		<br />
		{PAGINATION}
	</div>
{+END}
