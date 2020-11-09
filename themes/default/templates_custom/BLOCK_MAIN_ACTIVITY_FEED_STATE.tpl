{$REQUIRE_JAVASCRIPT,activity_feed}

<div id="status-updates" class="clearfix" data-view="BlockMainActivitiesState">
	{+START,IF_NON_EMPTY,{TITLE}}
		<h2 class="status-icon">{TITLE*}</h2>
	{+END}

	<form id="fp-status-form" class="js-form-status-updates" action="#!" method="post">
		{$INSERT_SPAMMER_BLACKHOLE}

		<input type="hidden" name="zone" value="{$?,{$ZONE},{$ZONE*},frontpage}" />
		<input type="hidden" name="page" value="{$PAGE*}" />

		<div class="status-controls">
			{+START,IF,{$ADDON_INSTALLED,chat}}
				<select name="privacy" size="1" class="form-control">
					<option selected="selected">
						{!PUBLIC}
					</option>
					<option>
						{!FRIENDS_ONLY}
					</option>
				</select>
			{+END}
			{+START,IF,{$NOT,{$ADDON_INSTALLED,chat}}}
				<input type="hidden" name="privacy" value="{!PUBLIC}" />
			{+END}
			<button data-disable-on-click="1" type="submit" class="btn btn-primary btn-scri buttons--save js-btn-submit-update" name="button" id="button">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!UPDATE}</button>
			<p id="activities-update-notify" class="activities-update-success js-el-activities-update-notification">254 {!activity_feed:CHARACTERS_LEFT}</p> {$,Do not remove; the AJAX notifications are inserted here.}
		</div>

		<div class="status-box-outer">
			<label class="accessibility-hidden" for="activity-status">{!TYPE_HERE}</label>
			<textarea class="form-control status-box fade-input field-input-non-filled js-textarea-activity-status" name="status" id="activity-status" rows="2">{!TYPE_HERE}</textarea>
		</div>
	</form>
</div>
