{$REQUIRE_JAVASCRIPT,core_menus}

<div data-tpl="pageLinkChooser" data-tpl-params="{+START,PARAMS_JSON,NAME,PAGE_TYPE}{_*}{+END}">
	{+START,IF_NON_PASSED_OR_FALSE,AS_FIELD}
	<form title="{!PRIMARY_PAGE_FORM}" action="{$BASE_URL*}/index.php" method="post">
		{$INSERT_FORM_POST_SECURITY}
	{+END}
		<div>
			<div class="accessibility-hidden"><label for="{NAME*}">{!ENTRY}</label></div>
			<input class="form-control js-input-page-link-chooser" style="display: none" type="text" id="{NAME*}" name="{NAME*}" value="{VALUE*}" />
			<div id="tree-list--root-{NAME*}">
				<!-- List put in here -->
			</div>
			<p class="associated-details">
				{!CLICK_ENTRY_POINT_TO_USE_2}
			</p>
		</div>
	{+START,IF_NON_PASSED_OR_FALSE,AS_FIELD}
	</form>
	{+END}
</div>
