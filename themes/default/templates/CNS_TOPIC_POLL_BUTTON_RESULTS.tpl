{+START,IF_PASSED,REVOKE_URL}
	<input type="hidden" name="revoke_vote" value="1" />
	<button type="submit" data-cms-confirm-click="{!POLL_REVOKE_VOTE_CONFIRM}" data-tpl="buttonScreenItem" data-form-action="{REVOKE_URL*}" class="js-change-poll-form btn btn-primary btn-scri buttons--cancel">{+START,INCLUDE,ICON}NAME=buttons/cancel{+END} <span>{!POLL_REVOKE_VOTE}</span></button>
{+END}
{+START,IF_PASSED,ALL_VOTES_URL}<a data-tpl="buttonScreenItem" data-open-as-overlay="{height: 600}" href="{ALL_VOTES_URL*}" class="btn btn-primary btn-scri buttons--archive">{+START,INCLUDE,ICON}NAME=admin/view_archive{+END} <span>{!POLL_VIEW_ALL_VOTES}</span></a>{+END}
