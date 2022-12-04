<section class="box box---block-side-calendar-listing"><div class="box-inner">
	<h3>{TITLE*}</h3>

	<div>{$,So that the titles don't have box titling CSS}
		{+START,IF_EMPTY,{DAYS}}
			<p class="nothing-here">{!NO_ENTRIES,event}</p>
		{+END}
		{+START,LOOP,DAYS}
			<h4 class="event-listing-day">{DATE*}</h4>

			<table class="map-table results-table wide-table events-listing-table autosized-table">
				<colgroup>
					<col class="event-listing-col-1" />
					<col class="event-listing-col-2" />
					<col class="event-listing-col-3" />
				</colgroup>

				<tbody>
					{+START,LOOP,EVENTS}
						<tr class="vevent" itemscope="itemscope" itemtype="http://schema.org/Event">
							<th>
								{+START,IF_PASSED,ICON}{+START,IF_PASSED,T_TITLE}
									<img width="24" height="24" src="{$IMG*,{ICON}}" title="{T_TITLE*}" alt="{T_TITLE*}" />
								{+END}{+END}
							</th>

							<td {+START,IF,{$EQ,{TIME_WRITTEN},{!ALL_DAY_EVENT}}} style="display: none"{+END}>
								<time class="dtstart" datetime="{TIME_VCAL*}" itemprop="startDate">{$?,{$EQ,{TIME_WRITTEN},{!ALL_DAY_EVENT}},{TIME_VCAL*},{TIME*}}</time>
							</td>

							<td{+START,IF,{$EQ,{TIME_WRITTEN},{!ALL_DAY_EVENT}}} colspan="2"{+END}>
								<a href="{VIEW_URL*}" class="url" itemprop="name"><span class="summary">{E_TITLE*}</span></a>
								{+START,IF_PASSED,TO_TIME}
									<span {+START,IF,{$EQ,{TIME},{TO_TIME}}} style="display: none"{+END}>
										<span class="associated-details">({!EVENT_ENDS_ON,<time class="dtend" datetime="{TO_TIME_VCAL*}" itemprop="endDate">{TO_TIME*}</time>})</span>
									</span>
								{+END}
							</td>
						</tr>
					{+END}
				</tbody>
			</table>
		{+END}
	</div>
</div></section>
