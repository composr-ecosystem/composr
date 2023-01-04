{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__HEADER}{+END}

	{+START,IF_NON_EMPTY,{CONTENT}}
		{$SET,ajax_block_main_multi_content_wrapper,ajax-block-main-multi-content-wrapper-{$RAND%}}
		{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
		<div id="{$GET*,ajax_block_main_multi_content_wrapper}" class="box-wrapper" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
			<div class="clearfix cguid-{_GUID|*} raw-ajax-grow-spot">
				<!-- TODO: This needs making looking good -->
				{+START,LOOP,CONTENT_DATA}
					<div class="multi-content-tile">
						{+START,IF_NON_EMPTY,{CONTENT_URL}}<a href="{CONTENT_URL*}">{+END}<img width="200" height="200" alt="" src="{$THUMBNAIL*,{CONTENT_IMAGE_URL},200x200,,,,crop}" />{+START,IF_NON_EMPTY,{CONTENT_URL}}</a>{+END}

						<div class="multi-content-tile-details">
							<span data-cms-tooltip="{ contents: '{CONTENT_TYPE_LABEL;^*}: {CONTENT_TITLE_PLAIN;^*}'}">{+START,IF_PASSED,CONTENT_TYPE_ICON}{+START,IF,{$NEQ,{CONTENT_IMAGE_URL},{CONTENT_TYPE_ICON}}}<img src="{CONTENT_TYPE_ICON*}" alt="{CONTENT_TYPE_LABEL}" width="24" /> {+END}{+END}{$TRUNCATE_LEFT,{CONTENT_TYPE_LABEL*}: {CONTENT_TITLE_PLAIN*},26}</span>

							{+START,IF_PASSED,CONTENT_AUTHOR}
								<br />{!SUBMITTED_BY,{CONTENT_AUTHOR*}}
							{+END}
							{+START,IF_NON_PASSED,CONTENT_AUTHOR}
								<br />{!SUBMITTED_BY,{CONTENT_USERNAME*}}
							{+END}

							{+START,IF_PASSED,_CONTENT_TIME}
								<br />{!_AGO*,{$MAKE_RELATIVE_DATE,{_CONTENT_TIME}}}
							{+END}
						</div>
					</div>
				{+END}
			</div>
		</div>
	{+END}

	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__FOOTER}{+END}
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,LOOP,CONTENT}
		{_loop_var}
	{+END}

	{+START,IF_PASSED,PAGINATION}
		{PAGINATION}
	{+END}
{+END}
