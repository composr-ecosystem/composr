<p>
	<button id="poll-vote-button" class="btn btn-primary btn-sm menu--social--polls" type="submit">{!VOTE}</button>
</p>

<p>
	<span class="associated-link"><a {+START,IF,{$NOT,{$HAS_PRIVILEGE,view_poll_results_before_voting}}} data-cms-confirm-click="{!VOTE_FORFEIGHT}"{+END} href="{RESULTS_URL*}">{!POLL_RESULTS}</a></span>
</p>
