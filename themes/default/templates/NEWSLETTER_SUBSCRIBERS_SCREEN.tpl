{TITLE}

{+START,IF_NON_EMPTY,{SUBSCRIBERS_TABLE_ROWS}}
	<table class="columned-table results-table wide-table autosized-table responsive-table">
		<thead>
			<tr>
				<th>{!EMAIL_ADDRESS}</th>
				<th>{!FORENAME}</th>
				<th>{!SURNAME}</th>
				<th>{!NAME}</th>
			</tr>
		</thead>
		<tbody>
			{SUBSCRIBERS_TABLE_ROWS}
		</tbody>
	</table>

	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="clearfix pagination-spacing">
			{PAGINATION}
		</div>
	{+END}
{+END}
{+START,IF_EMPTY,{SUBSCRIBERS_TABLE_ROWS}}
	<p class="nothing-here">
		{!NONE}
	</p>
{+END}

{+START,IF_NON_EMPTY,{DOMAINS}}
	<h2>{!DOMAIN_STATISTICS,{$INTEGER_FORMAT*,{DOMAINS},0},{$INTEGER_FORMAT*,{DOMAINS},0}}</h2>

	<table class="columned-table wide-table results-table">
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
					<td>{$INTEGER_FORMAT*,{_loop_var},0}</td>
				</tr>
			{+END}
		</tbody>
	</table>
{+END}
