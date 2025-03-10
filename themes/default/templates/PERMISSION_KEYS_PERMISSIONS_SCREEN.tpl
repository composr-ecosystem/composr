{$REQUIRE_JAVASCRIPT,core_permission_management}

<div data-tpl="permissionKeysPermissionsScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<p>
				{!PAGE_MATCH_KEY_ACCESS_TEXT}
			</p>

			<table class="columned-table wide-table results-table privileges responsive-table">
				<colgroup>
					<col class="match-key-name-column" />
					{COLS}
					<col class="permission-copy-column" />
				</colgroup>

				<thead>
				<tr>
					<th class="permission-header-cell">{!MATCH_KEY}</th>
					{HEADER_CELLS}
				</tr>
				</thead>

				<tbody>
				{ROWS}
				</tbody>
			</table>

			<h2>{!MATCH_KEY_MESSAGES}</h2>

			<p>
				{!PAGE_MATCH_KEY_MESSAGES_TEXT}
			</p>

			<table class="columned-table wide-table results-table responsive-table">
				<colgroup>
					<col class="match-key-name-column" />
					<col class="permission-match-key-message-column" />
				</colgroup>

				<thead>
				<tr>
					<th>
						{!MATCH_KEY}
					</th>
					<th>
						{!MATCH_KEY_MESSAGE_FIELD}
						<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,:userguide_comcode}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
					</th>
				</tr>
				</thead>

				<tbody>
				{ROWS2}
				</tbody>
			</table>

			<p class="proceed-button">
				<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save js-btn-hover-toggle-disable-size-change" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
			</p>
		</div>
	</form>
</div>
