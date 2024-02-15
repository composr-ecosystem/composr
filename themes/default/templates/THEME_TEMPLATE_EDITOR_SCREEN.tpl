{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,core_rich_media}
{$REQUIRE_JAVASCRIPT,core_themeing}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,actionlog}

<div data-tpl="themeTemplateEditorScreen" data-tpl-params="{+START,PARAMS_JSON,THEME,ACTIVE_GUID,LIVE_PREVIEW_URL,FILES_TO_LOAD}{_*}{+END}">
	{TITLE}

	{+START,IF,{$CONFIG_OPTION,editarea}}
		<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace.js"></script>
		<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace_composr.js"></script>
	{+END}

	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="#!">
		{$,Instruct Composr to pull in template data dynamically from the POST environment, i.e. do a live preview}
		<input type="hidden" name="template_preview_op" value="1" />

		{$INSERT_FORM_POST_SECURITY}

		<div class="template-editor-files-and-tabs">
			{$,Tree list}
			<div class="template-editor-file-selector">
				<div class="inner">
					<input type="hidden" id="theme_files" name="theme_files" value="{+START,IF_PASSED,DEFAULT_THEME_FILES_LOCATION}{DEFAULT_THEME_FILES_LOCATION*}{+END}" class="js-change-template-editor-add-tab-wrap" />
					<div id="tree-list--root-theme_files">
						<!-- List put in here -->
					</div>

					<p><button data-disable-on-click="1" data-click-pd="1" class="btn btn-primary btn-scri admin--add js-click-btn-add-template" type="submit" title="{!ADD_TEMPLATE_HELP}">{+START,INCLUDE,ICON}NAME=admin/add{+END} {!ADD_TEMPLATE}</button></p>
				</div>
			</div>

			{$,Tabs}
			<div class="template-editor-tabs">
				<div class="clearfix">
					<div class="clearfix">
						<div class="tabs spaced-out-tabs" role="tablist" id="template-editor-tab-headers"></div>
					</div>
					<div class="tab-surround" id="template-editor-tab-bodies"></div>
				</div>
			</div>
		</div>
	</form>
</div>
