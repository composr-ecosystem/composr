<div data-tpl="installerStep4SectionHide" data-tpl-params="{+START,PARAMS_JSON,TITLE}{_*}{+END}">
	<p class="lonely-label">
		<a class="toggleable-tray-button js-click-toggle-title-section" href="#!">{!ADVANCED_BELOW}:</a>
		<a class="toggleable-tray-button js-click-toggle-title-section" href="#!"><img id="img-{TITLE|*}" alt="{!EXPAND}: {$STRIP_TAGS,{TITLE}}" title="{!EXPAND}" width="24" height="24" src="{$BASE_URL*}/install.php?type=themes/default/images/icons/trays/expand.svg" /></a>
	</p>

	<div id="{TITLE|*}" style="display: none">
		{CONTENT}
	</div>
</div>
