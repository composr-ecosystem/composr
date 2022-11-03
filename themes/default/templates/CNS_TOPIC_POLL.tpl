{$REQUIRE_JAVASCRIPT,core_cns}
{$REQUIRE_JAVASCRIPT,cns_forum}

<form title="{!VOTE}" action="{VOTE_URL*}" method="post" data-tpl="cnsTopicPoll" data-tpl-params="{+START,PARAMS_JSON,MINIMUM_SELECTIONS,MAXIMUM_SELECTIONS}{_*}{+END}">
	{$INSERT_FORM_POST_SECURITY}
	<div class="wide-table-wrap"><div class="wide-table cns-topic autosized-table">
			<div class="cns-topic-post" data-tpl="cnsTopicPost">
				<div class="cns-topic-section cns-topic-header">
					<div class="cns-forum-box-left cns-post-poll">
						{!POLL}
					</div>

					<div class="cns-forum-box-right">
						<div class="cns-post-details">
							<div class="cns-post-details-date">
								<strong>{+START,FRACTIONAL_EDITABLE,{QUESTION},question,_SEARCH:topics:_edit_poll:{ID}}{QUESTION*}{+END}</strong>
							</div>
						</div>
					</div>
				</div>

				<div class="cns-topic-section cns-topic-body">
					<div class="cns-topic-post-member-details cns-post-poll" role="note">
						<div>
						</div>
					</div>

					<div class="cns-topic-post-area cns-post-main-column">
						<div class="clearfix">
							<table class="spaced-table cns-topic-poll">
								<tbody>
									{ANSWERS}
								</tbody>
							</table>

							{+START,IF,{$AND,{NOT_VOTED},{IS_OPEN}}}
								{+START,IF,{$GT,{_MAXIMUM_SELECTIONS},1}}
									{+START,IF,{$EQ,{_MINIMUM_SELECTIONS},{_MAXIMUM_SELECTIONS}}}
										<p class="vertical-alignment"><img alt="" class="inline-icon" src="{$IMG*,icons/status/notice}" /> <span>{!POLL_INVALID_SELECTION_COUNT_2,{MINIMUM_SELECTIONS*}}</span></p>
									{+END}
									{+START,IF,{$NEQ,{_MINIMUM_SELECTIONS},{_MAXIMUM_SELECTIONS}}}
										<p class="vertical-alignment"><img alt="" class="inline-icon" src="{$IMG*,icons/status/notice}" /> <span>{!POLL_INVALID_SELECTION_COUNT,{MINIMUM_SELECTIONS*},{MAXIMUM_SELECTIONS*}}</span></p>
									{+END}
								{+END}
								{+START,IF,{VOTES_REVEALED}}
									<p class="vertical-alignment"><img alt="" class="inline-icon" src="{$IMG*,icons/status/warn}" /> <span>{!TOPIC_POLL_MEMBER_VOTES_REVEALED}</span></p>
								{+END}
							{+END}
							{+START,IF,{$NEQ,{CLOSING_TIME},0}}
								<p class="vertical-alignment"><img alt="" class="inline-icon" src="{$IMG*,icons/status/inform}" /> <span>{!VOTING_CLOSES_IN,{$MAKE_RELATIVE_DATE*,{CLOSING_TIME},1,1}}</span></p>
							{+END}
							{+START,IF,{PRIVATE}}
								<p class="vertical-alignment"><img alt="" class="inline-icon" src="{$IMG*,icons/status/inform}" /> <span>{!TOPIC_POLL_RESULTS_HIDDEN}</span></p>
							{+END}
						</div>
					</div>
				</div>

				<div class="cns-topic-section cns-topic-footer">
					<div class="cns-left-post-buttons cns-post-poll">
					</div>

					<div class="buttons-group post-buttons cns-post-main-column">
						<div class="buttons-group-inner">
							{+START,IF,{SHOW_BUTTONS}}
								{BUTTONS}
							{+END}
							{+START,IF_PASSED,FOOTER_MESSAGE}
								{FOOTER_MESSAGE}
							{+END}
						</div>
					</div>
				</div>
			</div>
	</div></div>
</form>
