<form method="get" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,site:search}}" class="srhHold">
	{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,site:search:search_tutorials_external=1:search_comcode_pages=1:search_cns_posts=1:search_news=1:days=-1:all_defaults=0}}

	<label for="content" class="accessibility_hidden">Search</label>
	<input class="srhInp" type="text" id="content" name="content" placeholder="Search" value="" />

	<div class="srhBtn">
		<input type="submit" value="Search" />
	</div>
</form>
