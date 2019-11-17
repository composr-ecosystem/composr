{TITLE}

{+START,IF_NON_EMPTY,{SUBSCRIBERS_TABLE}}
	<div class="wide-table-wrap"><table class="columned-table results-table wide-table autosized-table responsive-table">
		<thead>
			<tr>
				<th>{!EMAIL_ADDRESS}</th>
				<th>{!FORENAME}</th>
				<th>{!SURNAME}</th>
				<th>{!NAME}</th>
			</tr>
		</thead>
		<tbody>
			{SUBSCRIBERS_TABLE}
		</tbody>
	</table></div>

	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="clearfix pagination-spacing">
			{PAGINATION}
		</div>
	{+END}
{+END}
{+START,IF_EMPTY,{SUBSCRIBERS_TABLE}}
	<p class="nothing-here">
		{!NONE}
	</p>
{+END}

{+START,IF_NON_EMPTY,{DOMAINS}}
	<h2>{!DOMAIN_STATISTICS,{$NUMBER_FORMAT*,{DOMAINS}},{$NUMBER_FORMAT*,{DOMAINS}}}</h2>

	<div class="wide-table-wrap"><table class="columned-table wide-table results-table">
		<thead>
			<tr>
				<th>{!DOMAIN}</th>
				<th>{!COUNT_TOTAL}</th>
			</tr>
		</thead>
		<tbody>
			{+START,LOOP,DOMAINS}
				<tr>
					<td>{_loop_key*}</td>
					<td>{$NUMBER_FORMAT*,{_loop_var}}</td>
				</tr>
			{+END}
		</tbody>
	</table></div>
{+END}
