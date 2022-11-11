{+START,IF_NON_EMPTY,{CONTENTS}}
	<div data-toggleable-tray="{}">
		<span class="side-galleries-block-exp toggleable-tray-title">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{CAPTION*}</a>
		</span>

		<ul style="display: none" class="compact-list toggleable-tray js-tray-content">
			{CONTENTS}
		</ul>
	</div>
{+END}
