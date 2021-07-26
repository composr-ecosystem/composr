{$SET,ajax_block_theme_template_editor_tab,ajax-block-theme-template-editor-tab-wrapper-{$RAND%}}

<div id="{$GET*,ajax_block_theme_template_editor_tab}" data-ajaxify="{ callUrl: '{$FIND_SCRIPT;*,snippet}?snippet=template_editor_load&amp;revisions_only=1', callParamsFromTarget: ['.*'], targetsSelector: '.ajax-block-wrapper-links .results-table-under a, .ajax-block-wrapper-links .results-table-under form' }">
	<div class="ajax-block-wrapper-links">
		{REVISIONS}
	</div>
</div>
