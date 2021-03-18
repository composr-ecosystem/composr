{+START,IF_EMPTY,{BANNERS}}
	<p class="nothing-here">{!NO_ENTRIES,banner}</p>
{+END}

{+START,IF_NON_EMPTY,{BANNERS}}
	<div class="wide-table-wrap"><table class="columned-table wide-table results-table spaced-table autosized-table responsive-table">
		<thead>
			<tr>
				<th>
					{!SITE}
				</th>
				<th>
					{!BANNER_HITS_FROM}
				</th>
				<th>
					{!BANNER_HITS_TO}
				</th>
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,BANNERS}
				<tr {+START,IF,{$LT,{_loop_key},5}} class="highlighted-table-cell"{+END}>
					<td>
						{+START,IF,{$LT,{_loop_key},20}}{BANNER}{+END}

						{+START,IF,{$NOT,{$LT,{_loop_key},20}}}
							{+START,IF_NON_EMPTY,{DESCRIPTION}}
								<p><a target="_blank" title="{$STRIP_TAGS,{DESCRIPTION}}: {!NEW_WINDOW}" href="{URL*}">{DESCRIPTION}</a></p>
							{+END}
							{+START,IF_EMPTY,{DESCRIPTION}}
								<p><a target="_blank" title="{NAME*}: {!NEW_WINDOW}" href="{URL*}">{NAME*}</a></p>
							{+END}
						{+END}
					</td>

					<td>
						{$INTEGER_FORMAT*,{HITS_FROM},0}
					</td>

					<td>
						{$INTEGER_FORMAT*,{HITS_TO},0}
					</td>
				</tr>
			{+END}
		</tbody>
	</table></div>
{+END}

{+START,IF_NON_EMPTY,{SUBMIT_URL}}
	<p class="proceed-button"><a class="btn btn-primary btn-scr admin--add" href="{SUBMIT_URL*}"><span>{+START,INCLUDE,ICON}NAME=admin/add{+END} {!ADD_BANNER}</span></a></p>
{+END}
