<h3>{TITLE*}</h3>

<div class="box box--ecom-products-prices-form-wrap" data-toggleable-tray="{}">
	<div class="box-inner">
		<h4 class="toggleable-tray-title js-tray-header">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" title="{!SETTINGS}: {!EXPAND}" href="#!">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=24
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" title="{!SETTINGS}: {!EXPAND}" href="#!">{!SETTINGS}</a>
		</h4>
		<div class="toggleable-tray js-tray-content" style="display: none" aria-expanded="false">
			{FORM}
		</div>
	</div>
</div>
