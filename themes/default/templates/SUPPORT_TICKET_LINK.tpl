<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}{+START,IF,{$AND,{$EQ,{LAST_POSTER_ID},{FIRST_POSTER_ID}},{$HAS_PRIVILEGE,support_operator},{$NOT,{CLOSED}}}} unclosed-ticket{+END}">
	<td>
		<a class="ticket-title" href="{URL*}">{+START,IF_EMPTY,{TITLE}}{!SUPPORT_TICKET}{+END}{TITLE*}</a>

		{+START,IF,{CLOSED}}
			<span class="closed-ticket">{!CLOSED}</span>
		{+END}

		<p class="block-desktop"><span class="field-name">{!COUNT_POSTS}:</span> {NUM_POSTS*}</p>

		{+START,IF_NON_EMPTY,{EXTRA_DETAILS}}
			<br />
			{EXTRA_DETAILS}
		{+END}
	</td>

	<td class="ticket-type">
		{TICKET_TYPE_NAME*}
	</td>

	{+START,IF,{$DESKTOP}}
		<td class="ticket-num-posts cell-desktop">
			{NUM_POSTS*}
		</td>
	{+END}

	<td>
		{+START,IF_NON_EMPTY,{FIRST_POSTER_PROFILE_URL}}
			<a class="ticket-first-poster" href="{FIRST_POSTER_PROFILE_URL*}">{FIRST_POSTER*}</a>
		{+END}
		{+START,IF_EMPTY,{FIRST_POSTER_PROFILE_URL}}
			{FIRST_POSTER*}
		{+END}
	</td>

	<td>
		<abbr class="ticket-age" title="{LAST_DATE*}">{$MAKE_RELATIVE_DATE*,{LAST_DATE_RAW}}</abbr>

		{+START,IF_NON_EMPTY,{LAST_POSTER_PROFILE_URL}}
			({!BY_SIMPLE_LOWER,<a class="ticket-last-poster" href="{LAST_POSTER_PROFILE_URL*}">{LAST_POSTER*}</a>})
		{+END}
		{+START,IF_EMPTY,{LAST_POSTER_PROFILE_URL}}
			({!BY_SIMPLE_LOWER,{LAST_POSTER*}})
		{+END}
	</td>

	<td>
		{+START,IF_NON_EMPTY,{ASSIGNED}}
			<ul class="horizontal-meta-details">
				{+START,LOOP,ASSIGNED}
					<li>{_loop_var*}</li>
				{+END}
			</ul>
		{+END}

		{+START,IF_EMPTY,{ASSIGNED}}
			<em>{!UNASSIGNED}</em>
		{+END}
	</td>

	{+START,IF_NON_EMPTY,{$GET,ticket_merge_into}}{+START,IF,{$HAS_PRIVILEGE,support_operator}}
		<td>
			<form title="{!MERGE_SUPPORT_TICKETS}" action="{$PAGE_LINK*,_SEARCH:tickets:merge:from={$GET,ticket_merge_into}:to={ID}}" method="post">
				{$INSERT_FORM_POST_SECURITY}

				<button class="btn btn-primary btn-scri admin--merge" type="submit" title="{!MERGE_SUPPORT_TICKETS}">{+START,INCLUDE,ICON}NAME=admin/merge{+END} {!_MERGE}</button>
			</form>
		</td>
	{+END}{+END}
</tr>
