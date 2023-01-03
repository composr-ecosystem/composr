<table class="spread-table calendar-month columned-table" itemprop="significantLinks">
	<colgroup>
		<col class="calendar-month-column-heading" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
		<col class="calendar-month-column-day" />
	</colgroup>

	<thead>
		<tr>
			<th></th>
			{+START,IF,{$SSW}}
				<th {+START,IF,{$MOBILE}} title="{!SUNDAY}"{+END}><span>{$?,{$MOBILE},{!FC_SUNDAY},{!SUNDAY}}</span></th>
			{+END}
			<th {+START,IF,{$MOBILE}} title="{!MONDAY}"{+END}><span>{$?,{$MOBILE},{!FC_MONDAY},{!MONDAY}}</span></th>
			<th {+START,IF,{$MOBILE}} title="{!TUESDAY}"{+END}><span>{$?,{$MOBILE},{!FC_TUESDAY},{!TUESDAY}}</span></th>
			<th {+START,IF,{$MOBILE}} title="{!WEDNESDAY}"{+END}><span>{$?,{$MOBILE},{!FC_WEDNESDAY},{!WEDNESDAY}}</span></th>
			<th {+START,IF,{$MOBILE}} title="{!THURSDAY}"{+END}><span>{$?,{$MOBILE},{!FC_THURSDAY},{!THURSDAY}}</span></th>
			<th {+START,IF,{$MOBILE}} title="{!FRIDAY}"{+END}><span>{$?,{$MOBILE},{!FC_FRIDAY},{!FRIDAY}}</span></th>
			<th {+START,IF,{$MOBILE}} title="{!SATURDAY}"{+END}><span>{$?,{$MOBILE},{!FC_SATURDAY},{!SATURDAY}}</span></th>
			{+START,IF,{$NOT,{$SSW}}}
				<th {+START,IF,{$MOBILE}} title="{!SUNDAY}"{+END}><span>{$?,{$MOBILE},{!FC_SUNDAY},{!SUNDAY}}</span></th>
			{+END}
		</tr>
	</thead>

	<tbody>
		{WEEKS}
	</tbody>
</table>
