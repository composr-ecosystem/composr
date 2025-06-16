{TITLE}

{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
	{$INSERT_FORM_POST_SECURITY}

	<h2>{!EXISTING_REDIRECTS}</h2>

	{+START,IF_NON_EMPTY,{EXISTING}}
		<table class="columned-table wide-table url-redirect-table results-table responsive-table">
			<colgroup>
				<col class="url-redirect-table-input-column" />
				<col class="url-redirect-table-input-column" />
				<col class="url-redirect-table-input-column" />
				<col class="url-redirect-table-type-column" />
				<col class="url-redirect-table-tick-column" />
			</colgroup>

			<thead>
				<tr>
					<th>
						{!FROM}
					</th>
					<th>
						{!TO}
					</th>
					<th>
						{!NOTES}
					</th>
					<th>
						{!TYPE}
					</th>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
				{EXISTING}
			</tbody>
		</table>
	{+END}
	{+START,IF_EMPTY,{EXISTING}}
		<p class="nothing_here">
			{!NO_ENTRIES}
		</p>
	{+END}

	<h2>{!NEW_REDIRECT}</h2>

	<table class="columned-table wide-table url-redirect-table results-table responsive-table">
		<colgroup>
			<col class="url-redirect-table-input-column" />
			<col class="url-redirect-table-input-column" />
			<col class="url-redirect-table-input-column" />
			<col class="url-redirect-table-type-column" />
			<col class="url-redirect-table-tick-column" />
		</colgroup>

		<thead>
			<tr>
				<th>
					{!FROM}
				</th>
				<th>
					{!TO}
				</th>
				<th>
					{!NOTES}
				</th>
				<th>
					{!TYPE}
				</th>
				<th>
				</th>
			</tr>
		</thead>
		<tbody>
			{NEW}
		</tbody>
	</table>

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="button-screen buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
	</p>
</form>
