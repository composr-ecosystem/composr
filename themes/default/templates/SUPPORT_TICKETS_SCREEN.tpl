{$REQUIRE_JAVASCRIPT,tickets}

<div data-tpl="supportTicketsScreen">
	{TITLE}

	{+START,IF_NON_EMPTY,{MESSAGE}}
		<p>{MESSAGE}</p>
	{+END}

	{+START,IF,{$NOT,{$IS_GUEST}}}
		<div class="box box---support-tickets-screen"><div class="box-inner vertical-alignment">
			<form title="{!FILTER}" class="clearfix" id="ticket-type-form" action="{$URL_FOR_GET_FORM*,{$SELF_URL,0,1}}" method="get">
				{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,1},ticket_type_id,open}

				<div class="clearfix ticket-filters">
					<div class="inline ticket-type-filter">
						<label class="field-name" for="ticket_type_id">{!TICKET_TYPE}:</label>
						<select id="ticket_type_id" name="ticket_type_id" class="form-control input-list-required">
							<option value="">&mdash;</option>
							{+START,LOOP,TYPES}
								<option value="{TICKET_TYPE_ID*}"{+START,IF,{SELECTED}} selected="selected"{+END}>{NAME*}</option>{$,You can also use {LEAD_TIME} to get the ticket type's lead time}
							{+END}
						</select>
					</div>

					<div class="inline spaced open-ticket-filter">
						<label class="field-name" for="open">{!OPEN_TICKETS_ONLY}:</label>
						<input type="checkbox" id="open" name="open" value="1"{+START,IF,{$_GET,open}} checked="checked"{+END} />
					</div>

					<div class="inline spaced filter-button">
						<button data-disable-on-click="1" class="btn btn-primary btn-scri buttons--filter js-scroll-to-top" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
					</div>
				</div>
			</form>
		</div></div>

		{+START,IF_EMPTY,{LINKS}}
			{$?,{$HAS_PRIVILEGE,support_operator},<p class="nothing-here">{!NO_ENTRIES}</p>,{$PARAGRAPH,{!SUPPORT_NO_TICKETS}}}
		{+END}
		{+START,IF_NON_EMPTY,{LINKS}}
			<div class="wide-table-wrap"><table class="columned-table results-table wide-table support-tickets autosized-table responsive-table">
				<thead>
					<tr>
						<th>
							{!SUPPORT_TICKET}
						</th>
						<th>
							{!TICKET_TYPE}
						</th>
						{+START,IF,{$DESKTOP}}
							<th class="cell-desktop">
								{!COUNT_POSTS}
							</th>
						{+END}
						<th>
							{!BY}
						</th>
						<th>
							{!LAST_POST}
						</th>
						<th>
							{!ASSIGNED_TO}
						</th>
					</tr>
				</thead>
				<tbody>
					{LINKS}
				</tbody>
			</table></div>
		{+END}
	{+END}

	<p class="buttons-group">
		<span class="buttons-group-inner">
			<a class="btn btn-primary btn-scr buttons--add-ticket" rel="add" href="{ADD_TICKET_URL*}"><span>{+START,INCLUDE,ICON}NAME=buttons/add_ticket{+END} {!ADD_TICKET}</span></a>
		</span>
	</p>
</div>
