{$SET,rndx,{$RAND}}

<li class="clearfix" data-toggleable-tray="{}">
	<div class="js-tray-header">
		<label for="banned_{$GET*,rndx}">
			<kbd>{MASK*}</kbd>
			{+START,IF,{$ADDON_INSTALLED,securitylogging}}
				<span class="horiz-field-sep"><em>{!BANNED}: <input type="checkbox" id="banned_{$GET*,rndx}" name="banned[]" value="{MASK*}"{+START,IF,{BANNED}} checked="checked"{+END} /></em></span>
			{+END}
		</label>

		<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{$?,{OPEN_DEFAULT},{!CONTRACT},{!EXPAND}}">{+START,INCLUDE,ICON}
			NAME=trays/{$?,{OPEN_DEFAULT},contract,expand}
			ICON_SIZE=20
		{+END}</a>
	</div>

	<div class="toggleable-tray js-tray-content" style="display: {$?,{OPEN_DEFAULT},block,none}"{+START,IF,{$NOT,{OPEN_DEFAULT}}} aria-expanded="false"{+END}>
		<ul>
			{GROUP}
		</ul>
	</div>
</li>
