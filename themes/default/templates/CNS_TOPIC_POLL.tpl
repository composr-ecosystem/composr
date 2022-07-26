{$REQUIRE_JAVASCRIPT,cns_forum}

<a id="poll-jump" rel="dovote"></a>
<form class="cns-topic-poll-form" title="{!VOTE}" action="{VOTE_URL*}" method="post" data-tpl="cnsTopicPoll" data-tpl-params="{+START,PARAMS_JSON,MINIMUM_SELECTIONS,MAXIMUM_SELECTIONS}{_*}{+END}">
	{$INSERT_FORM_POST_SECURITY}

	<h3>{+START,FRACTIONAL_EDITABLE,{QUESTION},question,_SEARCH:topics:_edit_poll:{ID}}{QUESTION*}{+END}</h3>

	<div class="wide-table-wrap">
		<table class="spread-table autosized-table cns-topic-poll wide-table">
			<tbody>
				{ANSWERS}
			</tbody>
		</table>

		<div class="cns-poll-meta">
			{+START,IF_NON_EMPTY,{BUTTON}}
				<div class="cns-poll-button">
					{BUTTON}
				</div>
			{+END}
			{+START,IF_NON_EMPTY,{PRIVATE}{NUM_CHOICES}}
				<div>
					{PRIVATE}
					{NUM_CHOICES}
				</div>
			{+END}
			{+START,IF_PASSED,CLOSING_TIME}
				<div class="cns-poll-closing-time">
					{!VOTING_CLOSES_IN,{$MAKE_RELATIVE_DATE*,{CLOSING_TIME},0,1}}
				</div>
			{+END}
		</div>
	</div>
</form>
