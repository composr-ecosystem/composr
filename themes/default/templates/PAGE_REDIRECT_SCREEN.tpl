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
		<table class="columned-table wide-table page-redirect-table results-table responsive-table">
			<colgroup>
				<col class="page-redirect-table-input-column" />
				<col class="page-redirect-table-input-column" />
				<col class="page-redirect-table-input-column" />
				<col class="page-redirect-table-input-column" />
				<col class="page-redirect-table-sup-column" />
				<col class="page-redirect-table-sup-column" />
			</colgroup>

			<thead>
				<tr>
					<th>
						{!REDIRECT_FROM_ZONE}
					</th>
					<th>
						{!REDIRECT_FROM_PAGE}
					</th>
					<th>
						{!REDIRECT_TO_ZONE}
					</th>
					<th>
						{!REDIRECT_TO_PAGE}
					</th>
					<th>
						<abbr title="{$STRIP_TAGS,{!IS_TRANSPARENT_REDIRECT}}">{!REDIRECT_TRANS_SHORT}</abbr>
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
		<p class="nothing-here">
			{!NO_ENTRIES}
		</p>
	{+END}

	<h2>{!NEW_REDIRECT}</h2>

	<table class="columned-table wide-table page-redirect-table results-table responsive-table">
		<colgroup>
			<col class="page-redirect-table-input-column" />
			<col class="page-redirect-table-input-column" />
			<col class="page-redirect-table-input-column" />
			<col class="page-redirect-table-input-column" />
			<col class="page-redirect-table-sup-column" />
			<col class="page-redirect-table-sup-column" />
		</colgroup>

		<thead>
			<tr>
				<th>
					{!REDIRECT_FROM_ZONE}
				</th>
				<th>
					{!REDIRECT_FROM_PAGE}
				</th>
				<th>
					{!REDIRECT_TO_ZONE}
				</th>
				<th>
					{!REDIRECT_TO_PAGE}
				</th>
				<th>
					<abbr title="{$STRIP_TAGS,{!IS_TRANSPARENT_REDIRECT}}">{!REDIRECT_TRANS_SHORT}</abbr>
				</th>
				<th>
				</th>
			</tr>
		</thead>
		<tbody>
			{NEW}
		</tbody>
	</table>

	<hr class="spaced-rule" />

	<div class="box box---redirecte-table-screen"><div class="box-inner">
		<h2>{!NOTES}</h2>

		<p>
			<label for="m-notes">{!NOTES_ABOUT_PAGE_REDIRECTS}</label>
		</p>

		<div>
			<textarea class="form-control form-control-wide" id="m-notes" name="notes" cols="50" rows="10">{NOTES*}</textarea>
		</div>
	</div></div>

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
	</p>
</form>
