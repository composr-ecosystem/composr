{+START,IF_NON_EMPTY,{CATEGORY_NAME}}
	<div data-toggleable-tray="{}">
		<h3 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{CATEGORY_NAME*}</a>
		</h3>

		<div class="toggleable-tray js-tray-content" style="display: {DISPLAY*}"{+START,IF,{$EQ,{DISPLAY},none}} aria-expanded="false"{+END}>
			<div class="clearfix radio-list-pictures">
				{CATEGORY}
			</div>
		</div>
	</div>
{+END}

{+START,IF_EMPTY,{CATEGORY_NAME}}
	<div class="clearfix theme-image--{$REPLACE,_,-,{FIELD_NAME|*}} radio-list-pictures">
		{CATEGORY}
	</div>
{+END}
