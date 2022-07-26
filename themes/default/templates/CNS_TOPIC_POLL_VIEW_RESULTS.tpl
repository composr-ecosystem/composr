{$REQUIRE_JAVASCRIPT,cns_forum}

<div class="cns-topic-poll-form">
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
				{PRIVATE}
				{NUM_CHOICES}
			{+END}
		</div>
	</div>
</div>
