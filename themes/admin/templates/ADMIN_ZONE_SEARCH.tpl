{$REQUIRE_JAVASCRIPT,admin}

{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin,adminzone}}
	<div class="adminzone-search" data-tpl="adminZoneSearch">
		<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,adminzone:admin:search}}" method="get" role="search" class="inline">
			<div id="adminzone-search-hidden" class="js-adminzone-search-hiddens">
				{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,adminzone:admin:search}}
			</div>

			<div>
				<label for="search-content" class="accessibility-hidden">{!SEARCH}</label>
				<input size="25" accesskey="=" type="search" id="search-content" name="content" class="form-control" placeholder="{!SEARCH}" />
				<div class="accessibility-hidden"><label for="new_window">{!NEW_WINDOW}</label></div>
				<input title="{!NEW_WINDOW}" type="checkbox" value="1" id="new_window" name="new_window" />
				<button type="submit" class="btn btn-primary btn-scri buttons--search js-click-btn-admin-search" data-tp-hiddens="{$HIDDENS_FOR_GET_FORM*,{$PAGE_LINK,adminzone:admin:search}}" data-tp-action-url="{$URL_FOR_GET_FORM*,{$PAGE_LINK,adminzone:admin:search}}">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {+START,IF,{$DESKTOP}}<span class="inline-desktop">{!SEARCH_ADMIN}</span>{+END}<span class="inline-mobile">{!SEARCH}</span></button>
				<button type="submit" class="btn btn-secondary btn-scri help js-click-btn-admin-search-tutorials" data-tp-hiddens="{$HIDDENS_FOR_GET_FORM*,{$BRAND_BASE_URL}/index.php?page=search&type=results}" data-tp-action-url="{$URL_FOR_GET_FORM*,{$BRAND_BASE_URL}/index.php?page=search&type=results}">{+START,INCLUDE,ICON}NAME=help{+END} {!SEARCH_TUTORIALS}</button>
			</div>
		</form>
	</div>
{+END}
