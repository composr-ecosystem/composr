{$REQUIRE_JAVASCRIPT,masonry}
{$REQUIRE_JAVASCRIPT,galleries}

{$SET,support_mass_select,cms_galleries}

{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
<div class="block-main-gallery-mosaic" data-tpl="blockMainGalleryMosaic" data-tpl-params="{+START,PARAMS_JSON,START,MAX,BLOCK_ID,block_call_url}{_*}{+END}">
	{+START,IF_NON_EMPTY,{ENTRIES}}
		<div class="gallery-actions">
			{+START,IF_PASSED,SLIDESHOW_URL}
			<a class="btn btn-primary btn-slideshow" rel="nofollow" {+START,IF,{$DESKTOP}}title="{!LINK_NEW_WINDOW*}" target="_blank"{+END} href="{SLIDESHOW_URL*}" data-link-start-slideshow="{}">{+START,INCLUDE,ICON}NAME=buttons/slideshow{+END} {!_SLIDESHOW}</a>
			{+END}

			{+START,IF_PASSED,SORTING}
				{$SET,show_sort_button,1}
				{SORTING}
			{+END}

			<div class="toggle-details mobile-only">
				<label for="toggle-details-{BLOCK_ID*}">{!SHOW_DETAILS}</label>
				<input type="checkbox" id="toggle-details-{BLOCK_ID*}" class="js-checkbox-toggle-details" name="toggle_details" />
			</div>
		</div>
	{+END}

	{$SET,ajax_block_main_gallery_mosaic_wrapper,ajax-block-main-gallery-embed-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_block_main_gallery_mosaic_wrapper}" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
		<div class="gallery-mosaic-items raw-ajax-grow-spot">
			{ENTRIES}
		</div>

		{+START,IF_NON_EMPTY,{PAGINATION}}
			<div class="pagination-spacing clearfix ajax-block-wrapper-links">
				{PAGINATION}
			</div>

			{+START,INCLUDE,AJAX_PAGINATION}
				WRAPPER_ID={$GET,ajax_block_main_gallery_mosaic_wrapper}
				ALLOW_INFINITE_SCROLL=1
			{+END}
		{+END}
	</div>

	{+START,INCLUDE,MASS_SELECT_DELETE_FORM}{+END}
</div>
{+END}

{+START,IF,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
	{ENTRIES}

	{PAGINATION}
{+END}

{$SET,support_mass_select,}

