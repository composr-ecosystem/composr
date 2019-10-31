{$REQUIRE_JAVASCRIPT,core_permission_management}

<div data-tpl="permissionKeysPermissionsScreen">
	{TITLE}

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
		{$INSERT_SPAMMER_BLACKHOLE}

		<div>
			<p>
				{!PAGE_MATCH_KEY_ACCESS_TEXT}
			</p>

			<div class="wide-table-wrap"><table class="columned-table wide-table results-table privileges responsive-table">
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
			</table></div>

			<h2>{!MATCH_KEY_MESSAGES}</h2>

			<p>
				{!PAGE_MATCH_KEY_MESSAGES_TEXT}
			</p>

			<div class="wide-table-wrap"><table class="columned-table wide-table results-table responsive-table">
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
			</table></div>

			<p class="proceed-button">
				<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save js-btn-hover-toggle-disable-size-change" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
			</p>
		</div>
	</form>
</div>
