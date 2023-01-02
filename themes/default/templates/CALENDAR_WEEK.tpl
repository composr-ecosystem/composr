<table class="spread-table calendar-week" itemprop="significantLinks">
	<thead>
		<tr>
			<th></th>
			{+START,IF,{$SSW}}
				<th{+START,IF,{$MOBILE}} title="{!SUNDAY}"{+END}><a href="{SUNDAY_URL*}">{$?,{$MOBILE},{!FC_SUNDAY},{SUNDAY_DATE*}}</a></th>
			{+END}
			<th{+START,IF,{$MOBILE}} title="{!MONDAY}"{+END}><a href="{MONDAY_URL*}">{$?,{$MOBILE},{!FC_MONDAY},{MONDAY_DATE*}}</a></th>
			<th{+START,IF,{$MOBILE}} title="{!TUESDAY}"{+END}><a href="{TUESDAY_URL*}">{$?,{$MOBILE},{!FC_TUESDAY},{TUESDAY_DATE*}}</a></th>
			<th{+START,IF,{$MOBILE}} title="{!WEDNESDAY}"{+END}><a href="{WEDNESDAY_URL*}">{$?,{$MOBILE},{!FC_WEDNESDAY},{WEDNESDAY_DATE*}}</a></th>
			<th{+START,IF,{$MOBILE}} title="{!THURSDAY}"{+END}><a href="{THURSDAY_URL*}">{$?,{$MOBILE},{!FC_THURSDAY},{THURSDAY_DATE*}}</a></th>
			<th{+START,IF,{$MOBILE}} title="{!FRIDAY}"{+END}><a href="{FRIDAY_URL*}">{$?,{$MOBILE},{!FC_FRIDAY},{FRIDAY_DATE*}}</a></th>
			<th{+START,IF,{$MOBILE}} title="{!SATURDAY}"{+END}><a href="{SATURDAY_URL*}">{$?,{$MOBILE},{!FC_SATURDAY},{SATURDAY_DATE*}}</a></th>
			{+START,IF,{$NOT,{$SSW}}}
				<th{+START,IF,{$MOBILE}} title="{!SUNDAY}"{+END}><a href="{SUNDAY_URL*}">{$?,{$MOBILE},{!FC_SUNDAY},{SUNDAY_DATE*}}</a></th>
			{+END}
		</tr>
	</thead>

	<tbody>
		{HOURS}
	</tbody>
</table>
