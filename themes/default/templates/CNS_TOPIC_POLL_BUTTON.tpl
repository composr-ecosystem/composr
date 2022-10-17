<button id="poll-vote-button" data-tpl="buttonScreenItem" data-form-action="{VOTE_URL*}" class="js-vote-poll btn btn-primary btn-scri menu--social--polls" type="submit">{+START,INCLUDE,ICON}NAME=menu/social/polls{+END} <span>{!VOTE}</span></button>
{+START,IF_PASSED_AND_TRUE,CAN_VIEW_RESULTS}
	<button data-tpl="buttonScreenItem" name="view_poll_results" value="1"{+START,IF,{VOTE_WILL_FORFEIGHT}} data-cms-confirm-click="{!VOTE_FORFEIGHT}"{+END} data-form-action="{RESULTS_URL*}" class="js-view-poll btn btn-primary btn-scri buttons--preview" type="submit">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} <span>{!POLL_RESULTS}</span></button>
{+END}
