<td class="calendar-year-month">
	<div class="block-mobile">
		<h2><a href="{MONTH_URL*}">{MONTH_NAME*}</a></h2>
	</div>

	<table class="spread-table calendar-year-month-table">
		<colgroup>
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
			<col class="calendar-weekly-column" />
		</colgroup>

		<thead>
			<tr>
				{+START,IF,{$SSW}}
					<th><span>{!FC_SUNDAY}</span></th>
				{+END}
				<th><span>{!FC_MONDAY}</span></th>
				<th><span>{!FC_TUESDAY}</span></th>
				<th><span>{!FC_WEDNESDAY}</span></th>
				<th><span>{!FC_THURSDAY}</span></th>
				<th><span>{!FC_FRIDAY}</span></th>
				<th><span>{!FC_SATURDAY}</span></th>
				{+START,IF,{$NOT,{$SSW}}}
					<th><span>{!FC_SUNDAY}</span></th>
				{+END}
			</tr>
		</thead>

		<tbody>
			{ENTRIES}
		</tbody>
	</table>
</td>
