{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,INCLUDE,BLOCK_MAIN_MULTI_CONTENT__HEADER}{+END}

	{+START,IF_NON_EMPTY,{CONTENT}}
		{$SET,ajax_block_main_multi_content_wrapper,ajax-block-main-multi-content-wrapper-{$RAND%}}
		{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
		<div id="{$GET*,ajax_block_main_multi_content_wrapper}" class="box-wrapper" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
			<div class="clearfix cguid-{_GUID|*} raw-ajax-grow-spot">
				<!-- TODO: This needs making looking good -->
				{+START,LOOP,CONTENT_DATA}
					<div style="float: left; position: relative">
						{+START,IF_NON_EMPTY,{CONTENT_URL}}<a href="{CONTENT_URL*}">{+END}<img width="200" height="200" alt="" src="{$THUMBNAIL*,{CONTENT_IMAGE_URL},200x200,,,,crop}" />{+START,IF_NON_EMPTY,{CONTENT_URL}}</a>{+END}

						<ul style="position: absolute; bottom: 0; background: rgba(255,255,255,0.6); margin-bottom: 0; padding-bottom: 0;">
							<li>{+START,IF_PASSED,CONTENT_TYPE_ICON}{+START,IF,{$NEQ,{CONTENT_IMAGE_URL},{CONTENT_TYPE_ICON}}}<img src="{CONTENT_TYPE_ICON*}" alt="{CONTENT_TYPE_LABEL}" /> {+END}{+END}{CONTENT_TITLE_HTML}</li>

							{+START,IF_PASSED,CONTENT_TIME}
								<li>{CONTENT_TIME_LABEL}: {CONTENT_TIME*}</li>
							{+END}

							{+START,IF_PASSED,CONTENT_AUTHOR}
								<li>{!AUTHOR}: {CONTENT_AUTHOR*}</li>
							{+END}
							{+START,IF_NON_PASSED,CONTENT_AUTHOR}
								<li>{!SUBMITTER}: {CONTENT_USERNAME*}</li>
							{+END}
						</ul>
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
