{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__HEADER}{+END}

	{$SET,ajax_block_main_multi_content_wrapper,ajax-block-main-multi-content-wrapper-{$RAND%}}
	{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
	<div id="{$GET*,ajax_block_main_multi_content_wrapper}" class="box-wrapper" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
		{+START,IF_NON_EMPTY,{CONTENT}}
			<div class="wide-table-wrap"><table class="cguid-{_GUID|*} raw-ajax-grow-spot columned-table wide-table results-table autosized-table responsive-table">
				<thead>
					<tr>
						{+START,LOOP,SHARED_KEYMAP_HEADINGS}
							<th>
								{_loop_var*}
							</th>
						{+END}
					</tr>
				</thead>

				<tbody>
					{+START,LOOP,CONTENT}
						<tr>
							{+START,LOOP,KEYMAP}
								<td>
									{_loop_var`}
								</td>
							{+END}
						</tr>
					{+END}
				</tbody>
			</table></div>
		{+END}

		{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__FOOTER}{+END}
	</div>
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,LOOP,CONTENT}
		{_loop_var}
	{+END}

	{+START,IF_PASSED,PAGINATION}
		{PAGINATION}
	{+END}
{+END}
