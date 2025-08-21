<div data-tpl="groupMemberTimeoutManageScreen">
	{TITLE}

	{$REQUIRE_JAVASCRIPT,ajax_people_lists}
	{$REQUIRE_JAVASCRIPT,core_form_interfaces}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<p>
		{!DOC_MANAGE_GROUP_MEMBER_TIMEOUTS}
	</p>

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
		{$INSERT_FORM_POST_SECURITY}

		<table class="columned-table results-table wide-table responsive-table autosized-table">
			<thead>
				<tr>
					<th>
						{!MEMBER}
					</th>
					<th>
						{!USERGROUP}
					</th>
					<th>
						{!GROUP_MEMBER_TIMEOUT_DATE}
					</th>
				</tr>
			</thead>
			<tbody>
				{+START,LOOP,TIMEOUTS}
					<tr>
						<td class="autocomplete-wrapper">
							<label class="accessibility-hidden" for="gmt_username_{_loop_key*}">{!USERNAME}</label>
							<input {+START,IF,{$MOBILE}} autocorrect="off"{+END} size="20" maxlength="255" class="form-control input-username-required js-focus-update-ajax-member-list js-keyup-update-ajax-member-list" type="text" id="gmt_username_{_loop_key*}" name="gmt_username_{_loop_key*}" value="{USERNAME*}" />
						</td>

						<td>
							<label class="accessibility-hidden" for="gmt_group_id_{_loop_key*}">{!USERGROUP}</label>
							<input name="gmt_old_group_id_{_loop_key*}" value="{GROUP_ID*}" type="hidden" />
							<select id="gmt_group_id_{_loop_key*}" name="gmt_group_id_{_loop_key*}" class="form-control">
								{+START,LOOP,GROUPS}
									<option value="{_loop_key*}"{+START,IF,{$EQ,{GROUP_ID},{_loop_key}}} selected="selected"{+END}>{_loop_var*}</option>
								{+END}
							</select>
						</td>

						<td>
							{DATE_INPUT}
						</td>
					</tr>
				{+END}

				<tr>
					<td class="autocomplete-wrapper">
						<label class="accessibility-hidden" for="gmt_username_new">{!USERNAME}</label>
						<input {+START,IF,{$MOBILE}} autocorrect="off"{+END} size="20" maxlength="255" class="form-control input-username-required js-focus-update-ajax-member-list js-keyup-update-ajax-member-list" type="text" id="gmt_username_new" name="gmt_username_new" />
					</td>

					<td>
						<label class="accessibility-hidden" for="gmt_group_id_new">{!USERGROUP}</label>
						<select id="gmt_group_id_new" name="gmt_group_id_new" class="form-control">
							<option value="">---</option>
							{+START,LOOP,GROUPS}
								<option value="{_loop_key*}">{_loop_var*}</option>
							{+END}
						</select>
					</td>

					<td>
						{DATE_INPUT}
					</td>
				</tr>
			</tbody>
		</table>

		<p class="proceed-button">
			<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
		</p>
	</form>
</div>