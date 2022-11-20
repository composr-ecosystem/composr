<div class="global-helper-panel-wrap" data-view="GlobalHelperPanel">
	<a id="helper-panel-toggle" href="#!" class="js-click-toggle-helper-panel" title="{!HELP_OR_ADVICE}: {$?,{$HIDE_HELP_PANEL},{!SHOW},{!HIDE}}">{+START,INCLUDE,ICON}
		NAME=helper_panel/{$?,{$HIDE_HELP_PANEL},show,hide}
		ICON_SIZE=14
	{+END}</a>

	<div class="block-mobile">
		<h2>{!HELP_OR_ADVICE}</h2>
	</div>

	<div id="helper-panel-contents"{+START,IF,{$HIDE_HELP_PANEL}} style="display: none"{+END} class="js-helper-panel-contents">
		{+START,IF,{$DESKTOP}}
			<div class="block-desktop">
				<h2>{!HELP_OR_ADVICE}</h2>
			</div>
		{+END}

		<div class="global-helper-panel">
			{+START,IF_NON_EMPTY,{$HELPER_PANEL_TEXT}}
				<div id="help" class="global-helper-panel-text">{$HELPER_PANEL_TEXT}</div>
			{+END}

			{+START,IF_NON_EMPTY,{$GET,HELPER_PANEL_TUTORIAL}}
				<div id="help-tutorial">
					<div class="box box---global-helper-panel--tutorial"><div class="box-inner">
						<div class="global-helper-panel-text">{!TUTORIAL_ON_THIS,{$TUTORIAL_URL*,{$GET,HELPER_PANEL_TUTORIAL}}}</div>
					</div></div>
				</div>
			{+END}

			{+START,IF_EMPTY,{$HELPER_PANEL_TEXT}{$GET,HELPER_PANEL_TUTORIAL}}
				<div id="help">
					<div class="box box---global-helper-panel--none"><div class="box-inner">
						<p>{!NO_HELP_HERE}</p>
					</div></div>
				</div>
			{+END}
		</div>
	</div>
</div>
