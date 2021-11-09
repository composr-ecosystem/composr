{+START,IF_EMPTY,{ISSUES}}
	<p class="nothing_here">
		{!FEATURES_NOTHING_YET}
	</p>
{+END}

{+START,IF_NON_EMPTY,{ISSUES}}
	<div class="tracker_issues">
		{+START,LOOP,ISSUES}
			<div class="box"><div class="box_inner">
				<h3>{CATEGORY*}: {SUMMARY*}</h3>

				<div class="tracker_issue_a">
					<p class="tracker_issue_votes">
						<strong>{!FEATURES_VOTES_lc,{VOTES*}}</strong>
					</p>

					{+START,IF,{VOTED}}
						<p class="tracker_issue_voting_status tracker_issue_not_voted" onclick="this.className='tracker_issue_voting_status tracker_issue_voted';">
							<a target="_blank" href="{UNVOTE_URL*}" title="{!FEATURES_UNVOTE} {!LINK_NEW_WINDOW}"><img src="{$IMG*,tracker/minus}" /> <span>{!FEATURES_UNVOTE}</span></a>
						</p>
					{+END}

					{+START,IF,{$NOT,{VOTED}}}
						<p class="tracker_issue_voting_status tracker_issue_voted">
							<a target="_blank" href="{VOTE_URL*}" title="{!FEATURES_VOTE} {!LINK_NEW_WINDOW}"><img src="{$IMG*,tracker/plus}" /> <span>{!FEATURES_VOTE}</span></a>
						</p>
					{+END}

					<p class="tracker_issue_progress">
						{!FEATURES_RAISED_PERCENT_OF,{PERCENTAGE*},{CREDITS*}}

						{+START,IF_PASSED,COST}
							<br />
							<span class="associated_details">({!FEATURES_CREDITS_HOURS_COST,{CREDITS*},{HOURS*},{$TRIM,{COST}}})</span>
						{+END}
					</p>
				</div>

				<div class="tracker_issue_b">
					<p class="tracker_issue_description">
						{$TRUNCATE_LEFT,{DESCRIPTION},310,1,1}
					</p>

					<p class="associated_details tracker_issue_poster">
						{!FEATURES_SUGGESTED_BY,{MEMBER_LINK},{DATE*}}
					</p>

					<p class="associated_link_to_small tracker_issue_link">
						&raquo; <a href="{FULL_URL*}">{!FEATURES_FULL_DETAILS}</a> ({!FEATURES_COMMENTS_lc,{NUM_COMMENTS*}})
					</p>
				</div>
			</div></div>
		{+END}
	</div>
{+END}

{+START,IF_NON_EMPTY,{PAGINATION}}
	<div class="float_surrounder">
		<br />
		{PAGINATION}
	</div>
{+END}
