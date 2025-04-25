{$REQUIRE_JAVASCRIPT,cms_homesite_tracker}

{+START,IF_EMPTY,{ISSUES}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}

{+START,IF_NON_EMPTY,{ISSUES}}
	<div class="tracker-issues" data-tpl="mantisTracker">
		{+START,LOOP,ISSUES}
			<div class="box"><div class="box-inner">
				<h3>{CATEGORY*}: {SUMMARY*}</h3>

				<div class="float-surrounder">
					<div class="tracker-issue-a">
						<p class="tracker-issue-points">
							{!POINTS_SPONSORED,{POINTS_RAISED*}}
						</p>

						{+START,IF_NON_EMPTY,{COST}}
							<p class="tracker-issue-progress">
								<span class="associated-details">({!SUGGESTED_TOTAL_SPONSORSHIP,{$TRIM,{COST}}})</span>
							</p>
						{+END}

						{+START,IF,{VOTED}}
							<p class="js-click-add-voted-class tracker-issue-voting-status tracker-issue-not-voted">
								<a target="_blank" href="{UNVOTE_URL*}" title="{!UNMONITOR} {!LINK_NEW_WINDOW}"><img width="16" height="16" src="{$IMG*,icons/tracker/minus}" /> <span>{!UNMONITOR}</span></a>
							</p>
						{+END}

						{+START,IF,{$NOT,{VOTED}}}
							<p class="tracker-issue-voting-status tracker-issue-voted">
								<a target="_blank" href="{VOTE_URL*}" title="{!MONITOR} {!LINK_NEW_WINDOW}"><img width="16" height="16" src="{$IMG*,icons/tracker/plus}" /> <span>{!MONITOR}</span></a>
							</p>
						{+END}

						<p class="tracker-issue-votes">
							{!VOTES,{VOTES*}}
						</p>
					</div>

					<div class="tracker-issue-b">
						<p class="tracker-issue-description">
							{$TRUNCATE_LEFT,{DESCRIPTION},310,1,1}
						</p>

						<p class="associated-details tracker-issue-poster">
							{!ISSUE_REPORTER,{MEMBER_LINK},{DATE*}}
						</p>

						<p class="associated-link-to-small tracker-issue-link">
							&raquo; <a href="{FULL_URL*}" target="_blank" title="{!ISSUE_FULL_DETAILS} ({!LINK_NEW_WINDOW})">{!ISSUE_FULL_DETAILS}</a> ({!ISSUE_COMMENTS,{NUM_COMMENTS*}})
						</p>
					</div>
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
