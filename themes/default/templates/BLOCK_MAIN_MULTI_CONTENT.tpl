{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,SET,links}
		{+START,IF_NON_EMPTY,{SUBMIT_URL}{ARCHIVE_URL}}
			<ul class="horizontal_links associated_links_block_group">
				{+START,IF_NON_EMPTY,{SUBMIT_URL}}
					<li><a rel="add" href="{SUBMIT_URL*}">{ADD_STRING*}</a></li>
				{+END}
				{+START,IF_NON_EMPTY,{CONTENT}}
					{+START,IF_NON_EMPTY,{ARCHIVE_URL}}
						<li><a href="{ARCHIVE_URL*}" title="{!ARCHIVES}: {TYPE*}">{!ARCHIVES}</a></li>
					{+END}
				{+END}
			</ul>
		{+END}
	{+END}

	{$,Example carousel layout if the 'carousel' GUID was passed}
	{$,With some basic templating you could also achieve simple lists or tables}
	{+START,IF,{$EQ,{_GUID},carousel}}
		{$REQUIRE_JAVASCRIPT,dyn_comcode}
		{$REQUIRE_CSS,carousels}

		{$SET-,carousel_id,{$RAND}}

		{+START,IF_NON_EMPTY,{CONTENT}}
			<div id="carousel_{$GET*,carousel_id}" class="carousel" style="display: none">
				<div class="move_left" onkeypress="this.onmousedown(event);" onmousedown="carousel_move({$GET*,carousel_id},-30); return false;"></div>
				<div class="move_right" onkeypress="this.onmousedown(event);" onmousedown="carousel_move({$GET*,carousel_id},+30); return false;"></div>

				<div class="main raw_ajax_grow_spot">
				</div>
			</div>

			<div class="carousel_temp" id="carousel_ns_{$GET*,carousel_id}">
				{+START,LOOP,CONTENT}
					{_loop_var}
				{+END}
			</div>

			<script>// <![CDATA[
				add_event_listener_abstract(window,'load',function() {
					initialise_carousel({$GET,carousel_id});

					var current_loading_from_pos_{$GET*,carousel_id}={START%};

					function carousel_prepare_load_more_{$GET*,carousel_id}(carousel_id)
					{
						var ob=document.getElementById('carousel_ns_'+carousel_id);

						if (ob.parentNode.scrollLeft+find_width(ob)*2<ob.scrollWidth) return; // Not close enough to need more results

						current_loading_from_pos_{$GET*,carousel_id}+={MAX%};

						call_block(
							'{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS},raw=.*\,cache=.*}'+'&{START_PARAM%}='+current_loading_from_pos_{$GET*,carousel_id},
							'raw=1,cache=0',
							ob,
							true
						);
					}
				});
			//]]></script>
		{+END}

		{+START,IF_EMPTY,{CONTENT}}
			<p class="nothing_here">{!NO_ENTRIES,{CONTENT_TYPE}}</p>
		{+END}

		{$GET,links}
	{+END}

	{$,Normal sequential box layout}
	{$,With some very basic CSS you could also achieve grid layouts}
	{+START,IF,{$NEQ,{_GUID},carousel}}
		{$SET-,ajax_block_main_multi_content_wrapper,ajax_block_main_multi_content_wrapper_{$RAND%}}
		<div id="{$GET*,ajax_block_main_multi_content_wrapper}" class="box_wrapper">
			{+START,IF_NON_EMPTY,{TITLE}}
				<h2>{TITLE*}</h2>
			{+END}

			<div class="float_surrounder cguid_{_GUID|*} raw_ajax_grow_spot">
				{+START,IF_NON_EMPTY,{CONTENT}}
					{+START,LOOP,CONTENT}
						{_loop_var}
					{+END}
				{+END}

				{+START,IF_EMPTY,{CONTENT}}
					<p class="nothing_here">{!NO_ENTRIES,{CONTENT_TYPE}}</p>
				{+END}
			</div>

			{+START,IF_PASSED,PAGINATION}
				{+START,IF_NON_EMPTY,{PAGINATION}}
					<div class="pagination_spacing float_surrounder ajax_block_wrapper_links">
						{PAGINATION}
					</div>

					{+START,INCLUDE,AJAX_PAGINATION}
						ALLOW_INFINITE_SCROLL=1
						WRAPPER_ID={$GET,ajax_block_main_multi_content_wrapper}
					{+END}
				{+END}
			{+END}

			{$GET,links}
		</div>
	{+END}
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,LOOP,CONTENT}
		{_loop_var}
	{+END}

	{+START,IF_PASSED,PAGINATION}
		{PAGINATION}
	{+END}
{+END}
