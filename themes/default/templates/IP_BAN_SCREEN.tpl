<div data-tpl="ipBanScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

	<p>
		{!DESCRIPTION_BANNED_ADDRESSES_A}
	</p>

	<p>
		{!DESCRIPTION_BANNED_ADDRESSES_B}
	</p>

	<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<p class="lonely-label"><label for="bans">{!BANNED_ADDRESSES}:</label></p>
		<div>
			<textarea cols="30" rows="14" class="form-control form-control-wide textarea-scroll" id="bans" name="bans">{BANS*}</textarea>
		</div>

		<p class="lonely-label"><label for="locked_bans">{!EXTERNALLY_BANNED_ADDRESSES}:</label></p>
		<div>
			<textarea readonly="readonly" cols="30" rows="14" class="form-control form-control-wide textarea-scroll" id="locked_bans" name="locked_bans">{LOCKED_BANS*}</textarea>
		</div>

		<p class="lonely-label"><label for="unbannable">{!UNBANNABLE_IP_ADDRESSES}:</label></p>
		<div>
			<textarea cols="30" rows="14" class="form-control form-control-wide textarea-scroll" id="unbannable" name="unbannable">{UNBANNABLE*}</textarea>
		</div>

		<p class="proceed-button">
			<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
		</p>
	</form>
</div>
