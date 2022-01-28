{$SET,support_mass_select,cms_galleries}

{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{+START,IF,{$NEQ,{_GUID},carousel}}
		{+START,IF_NON_EMPTY,{ENTRIES}{PAGINATION}}
			{$SET,ajax_block_main_gallery_embed_wrapper,ajax_block_main_gallery_embed_wrapper_{$RAND%}}
			<div id="{$GET*,ajax_block_main_gallery_embed_wrapper}">
				<div class="gallery_grid_cell_wrap raw_ajax_grow_spot">
					{ENTRIES}
				</div>

				{+START,IF_NON_EMPTY,{PAGINATION}}
					<div class="pagination_spacing float_surrounder ajax_block_wrapper_links">
						{PAGINATION}
					</div>

					{+START,INCLUDE,AJAX_PAGINATION}
						ALLOW_INFINITE_SCROLL=1
						WRAPPER_ID={$GET,ajax_block_main_gallery_embed_wrapper}
					{+END}
				{+END}
			</div>
		{+END}
	{+END}

	{+START,IF,{$EQ,{_GUID},carousel}}
		{$REQUIRE_JAVASCRIPT,dyn_comcode}
		{$REQUIRE_CSS,carousels}

		{$SET,carousel_id,{$RAND}}

		<div id="carousel_{$GET*,carousel_id}" class="carousel" style="display: none">
			<div class="move_left" onkeypress="this.onmousedown(event);" onmousedown="carousel_move({$GET*,carousel_id},-47); return false;"></div>
			<div class="move_right" onkeypress="this.onmousedown(event); this.onclick(event);" onclick="carousel_prepare_load_more_{$GET*,carousel_id}({$GET*,carousel_id});" onmousedown="carousel_move({$GET*,carousel_id},+47); return false;"></div>

			<div class="main raw_ajax_grow_spot" id="carousel_{$GET*,carousel_id}_container">
			</div>
		</div>

		<div class="carousel_temp" id="carousel_ns_{$GET*,carousel_id}">
			{ENTRIES}
		</div>

		<script>// <![CDATA[
			add_event_listener_abstract(window,'load',function() {
				initialise_carousel({$GET,carousel_id});
			});

			var current_loading_from_pos_{$GET*,carousel_id}={START%};

			function carousel_prepare_load_more_{$GET*,carousel_id}(carousel_id)
			{
				var ob=document.getElementById('carousel_ns_'+carousel_id);

				if (ob.parentNode.scrollLeft+find_width(ob)*2<ob.scrollWidth) return; // Not close enough to need more results

				current_loading_from_pos_{$GET*,carousel_id}+={MAX%};

				call_block(
					'{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS},raw=.*\,cache=.*,{START_PARAM%}=.*}'+'&{START_PARAM%}='+current_loading_from_pos_{$GET*,carousel_id},
					'raw=1,cache=0',
					ob,
					true
				);
			}
		//]]></script>
	{+END}

	{+START,IF_NON_EMPTY,{ENTRIES}{PAGINATION}}
		{+START,INCLUDE,MASS_SELECT_DELETE_FORM}
		{+END}
	{+END}
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{ENTRIES}

	{PAGINATION}
{+END}

{$SET,support_mass_select,}
