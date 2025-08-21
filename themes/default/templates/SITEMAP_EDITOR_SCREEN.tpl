{$REQUIRE_JAVASCRIPT,page_management}

{$SET,edit_zone_url,{$PAGE_LINK,_SEARCH:admin_zones:_edit:!}}
{$SET,add_zone_url,{$PAGE_LINK,_SEARCH:admin_zones:add}}
{$SET,zone_editor_url,{$PAGE_LINK,_SEARCH:admin_zones:_editor:!}}
{$SET,permission_tree_editor_url,{$PAGE_LINK,_SEARCH:admin_permissions:browse:!}}
{$SET,edit_page_url,{$PAGE_LINK,cms:cms_comcode_pages:_edit:page_link=!}}
{$SET,add_page_url,{$PAGE_LINK,_SEARCH:cms_comcode_pages:_edit:page_link=!:example}}
{$SET,delete_url,{$PAGE_LINK,_SELF:_SELF:delete:zone=[1]:page__[2]=1}}
{$SET,stats_url,{+START,IF,{$ADDON_INSTALLED,stats}}{$PAGE_LINK,_SEARCH:admin_stats:_page:iscreen=!}{+END}}

<div data-tpl="sitemapEditorScreen" data-tpl-params="{+START,PARAMS_JSON,edit_zone_url,add_zone_url,zone_editor_url,permission_tree_editor_url,edit_page_url,add_page_url,delete_url,stats_url}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<div class="clearfix">
		<div class="sitemap-right">
			<div class="box box---sitemap-editor-screen"><div class="box-inner">
				<h2>{!SELECTION_DETAILS}</h2>

				<div id="details-target">
					{!NO_ENTRY_POINT_SELECTED}
				</div>
			</div></div>
		</div>

		<div class="sitemap-left">
			<div class="box box---sitemap-editor-screen"><div class="box-inner">
				<h2>{!SITEMAP}</h2>

				<div class="accessibility-hidden"><label for="tree-list">{!ENTRY}</label></div>
				<form title="{!PRIMARY_PAGE_FORM}" action="#" method="post">
					{$INSERT_FORM_POST_SECURITY}

					<input style="display: none" type="text" id="tree-list" name="tree_list" value="{$_GET*,id}" class="form-control js-change-update-details-box" />
					<div id="tree-list--root-tree-list" class="sitemap-editor">
						<!-- List put in here -->
					</div>
				</form>
			</div></div>
		</div>
	</div>

	{+START,IF,{$ADDON_INSTALLED,redirects_editor}}
		<p>
			{!PAGE_DRAG,{$PAGE_LINK*,_SEARCH:admin_redirects}}
		</p>
	{+END}
</div>