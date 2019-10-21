<table class="spread-table calendar-week spaced-table autosized-table columned-table" itemprop="significantLinks">
	<thead>
		<tr>
			<th></th>
			{+START,IF,{$SSW}}
				<th><a href="{SUNDAY_URL*}">{$?,{$MOBILE},{!FC_SUNDAY},{SUNDAY_DATE*}}</a></th>
			{+END}
			<th><a href="{MONDAY_URL*}">{$?,{$MOBILE},{!FC_MONDAY},{MONDAY_DATE*}}</a></th>
			<th><a href="{TUESDAY_URL*}">{$?,{$MOBILE},{!FC_TUESDAY},{TUESDAY_DATE*}}</a></th>
			<th><a href="{WEDNESDAY_URL*}">{$?,{$MOBILE},{!FC_WEDNESDAY},{WEDNESDAY_DATE*}}</a></th>
			<th><a href="{THURSDAY_URL*}">{$?,{$MOBILE},{!FC_THURSDAY},{THURSDAY_DATE*}}</a></th>
			<th><a href="{FRIDAY_URL*}">{$?,{$MOBILE},{!FC_FRIDAY},{FRIDAY_DATE*}}</a></th>
			<th><a href="{SATURDAY_URL*}">{$?,{$MOBILE},{!FC_SATURDAY},{SATURDAY_DATE*}}</a></th>
			{+START,IF,{$NOT,{$SSW}}}
				<th><a href="{SUNDAY_URL*}">{$?,{$MOBILE},{!FC_SUNDAY},{SUNDAY_DATE*}}</a></th>
			{+END}
		</tr>
	</thead>

	<tbody>
		{HOURS}
	</tbody>
</table>
