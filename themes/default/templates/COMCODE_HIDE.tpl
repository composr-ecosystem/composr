<blockquote class="box box--comcode-hide" data-toggleable-tray="{}">
	<div class="box-inner">
		<h4 class="toggleable-tray-title">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=24
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{TEXT}</a>
		</h4>

		<div class="toggleable-tray js-tray-content" style="display: none">
			{CONTENT}
		</div>
	</div>
</blockquote>
