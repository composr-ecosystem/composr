{$REQUIRE_JAVASCRIPT,core_cns}
{$REQUIRE_JAVASCRIPT,cns_forum}

<form title="{!POLL_REVOKE_VOTE}" action="{REVOKE_URL*}" method="post" data-tpl="cnsTopicPoll">
	{$INSERT_FORM_POST_SECURITY}
	<div class="wide-table-wrap"><div class="wide-table cns-topic autosized-table">
			<div class="cns-topic-post" data-tpl="cnsTopicPost">
				<div class="cns-topic-section cns-topic-header">
					<div class="cns-forum-box-left cns-post-poll">
						{!POLL}
					</div>

					<div class="cns-forum-box-right cns-post-details">
						<div class="cns-post-details-date">
							<strong>{+START,FRACTIONAL_EDITABLE,{QUESTION},question,_SEARCH:topics:_edit_poll:{ID}}{QUESTION*}{+END}</strong>
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
								{+START,IF,{$NEQ,{CLOSING_TIME},0}}
									<p class="vertical-alignment"><img class="inline-icon" src="{$IMG,icons/status/inform}" /> <span>{!VOTING_CLOSES_IN,{$MAKE_RELATIVE_DATE*,{CLOSING_TIME},1,1}}</span></p>
								{+END}
								{+START,IF,{PRIVATE}}
									<p class="vertical-alignment"><img class="inline-icon" src="{$IMG,icons/status/inform}" /> <span>{!TOPIC_POLL_RESULTS_HIDDEN}</span></p>
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