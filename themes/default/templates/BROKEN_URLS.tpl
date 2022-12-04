{+START,IF_NON_EMPTY,{URLS}}
	<table class="columned-table wide-table results-table autosized-table zebra">
		<thead>
			<tr>
				<th>
					{!URL}
				</th>

				<th>
					{!TABLE_NAME}
				</th>

				<th>
					{!FIELD_NAME}
				</th>

				<th>
					{!IDENTIFIER}
				</th>

				{+START,IF,{DONE}}
					<th>
						{!STATUS}
					</th>
				{+END}
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,URLS}
				<tr class="zebra-{$CYCLE*,urls,0,1}">
					<td>
						<a href="{FULL_URL*}">{FULL_URL*}</a>
					</td>

					<td>
						{+START,LOOP,TABLE_NAMES}
							{+START,IF,{$NEQ,{_loop_key},0}}<br />{+END}

							{+START,IF_PASSED,_loop_var}
								{_loop_var*}
							{+END}
							{+START,IF_NON_PASSED,_loop_var}
								<em>{+START,OF,{_loop_key}}CONTENT_TYPE{+END}</em>
							{+END}
						{+END}
					</td>

					<td>
						{+START,LOOP,FIELD_NAMES}
							{+START,IF,{$NEQ,{_loop_key},0}}<br />{+END}

							{+START,IF_PASSED,_loop_var}
								{_loop_var*}
							{+END}
							{+START,IF_NON_PASSED,_loop_var}
								<em>{!NA}</em>
							{+END}
						{+END}
					</td>

					<td>
						{+START,LOOP,IDENTIFIERS}
							{+START,IF,{$NEQ,{_loop_key},0}}<br />{+END}

							{+START,IF_PASSED,IDENTIFIER}
								{+START,IF_PASSED,EDIT_URL}
									<a href="{EDIT_URL*}">{IDENTIFIER*}</a>
								{+END}
								{+START,IF_NON_PASSED,EDIT_URL}
									{IDENTIFIER*}
								{+END}
							{+END}
							{+START,IF_NON_PASSED,IDENTIFIER}
								<em>{!NA}</em>
							{+END}
						{+END}
					</td>

					{+START,IF,{DONE}}
						<td>
							{+START,IF_PASSED,STATUS}
								{+START,IF,{STATUS}}
									<span class="multilist-mark yes">&#10003;</span> {$,Checkmark entity}
								{+END}
								{+START,IF,{$NOT,{STATUS}}}
									<span class="multilist-mark no">&#10007;</span> {$,Cross entity}
								{+END}
							{+END}
							{+START,IF_NON_PASSED,STATUS}
								<em>{!NA}</em>
							{+END}

							{+START,IF_PASSED,MESSAGE}
								({MESSAGE*})
							{+END}
						</td>
					{+END}
				</tr>
			{+END}
		</tbody>
	</table>
{+END}

{+START,IF_EMPTY,{URLS}}
	<p class="nothing-here">{!NO_RESULTS}</p>
{+END}
