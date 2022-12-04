{TITLE}

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
	{$INSERT_FORM_POST_SECURITY}

	<table class="form-table columned-table wide-table privileges responsive-table responsive-table-bolded-first-column">
		<colgroup>
			<col class="permission-field-name-column" />
			{COLS}
			<col class="permission-copy-column" />
		</colgroup>

		<thead>
			<tr>
				<th></th>
				{HEADER_CELLS}
			</tr>
		</thead>

		<tbody>
			{ROWS}
		</tbody>
	</table>

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
	</p>
</form>
