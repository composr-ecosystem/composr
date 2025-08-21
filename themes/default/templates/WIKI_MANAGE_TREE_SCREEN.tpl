<div data-tpl="wikiManageTreeScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<h2>{!HELP}</h2>

	{+START,SET,advanced_help}
		<div class="ttb-left-spaced" data-toggleable-tray="{}">
			<div class="toggleable-tray-unstyled">
				<a class="toggleable-tray-button ttb-left js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
					NAME=trays/expand
					ICON_SIZE=20
				{+END}</a>
				<a class="toggleable-tray-button ttb-light js-tray-onclick-toggle-tray" href="#!">{!WIKI_MANAGE_TREE_TEXT_ADVANCED_LABEL}</a>:
			</div>

			<div class="toggleable-tray js-tray-content" style="display: none">
				{!WIKI_MANAGE_TREE_TEXT_ADVANCED}
			</div>
		</div>
	{+END}

	{!WIKI_MANAGE_TREE_TEXT,{$GET,advanced_help},{PAGE_TITLE*}}

	<h2>{!SETTINGS}</h2>

	{FORM}

	{+START,IF_NON_EMPTY,{WIKI_TREE}}
		<h2>{!ID_ASSISTANCE_TOOL}</h2>

		<form title="{!PRIMARY_PAGE_FORM}" method="post" action="#">
			{$INSERT_FORM_POST_SECURITY}

			<p><label for="mtp-tree">{!BROWSE_ID_INSERT}</label></p>

			<div>
				{$REQUIRE_JAVASCRIPT,tree_list}
				{$REQUIRE_JAVASCRIPT,wiki}

				<input style="display: none" type="text" id="mtp-tree" name="tree" class="form-control js-change-input-tree-update-children-value" />
				<div id="tree-list--root-mtp-tree">
					<!-- List put in here -->
				</div>
			</div>
		</form>
	{+END}
</div>