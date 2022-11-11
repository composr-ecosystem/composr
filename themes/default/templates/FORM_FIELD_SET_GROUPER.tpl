<div class="box box---form-field-set-grouper" data-toggleable-tray="{}">
	<div class="box-inner">
		<h2 class="toggleable-tray-title">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{+START,IF_PASSED_AND_TRUE,VISIBLE}{!CONTRACT}{+END}{+START,IF_NON_PASSED_OR_FALSE,VISIBLE}{!EXPAND}{+END}">{+START,INCLUDE,ICON}
				NAME=trays/{+START,IF_PASSED_AND_TRUE,VISIBLE}contract{+END}{+START,IF_NON_PASSED_OR_FALSE,VISIBLE}expand{+END}
				ICON_SIZE=20
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{NAME*}</a>
		</h2>

		<div class="toggleable-tray js-tray-content" id="{ID*}"{+START,IF_NON_PASSED_OR_FALSE,VISIBLE} style="display: none" aria-expanded="false"{+END}>
			<div class="wide-table-wrap"><table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="field-name-column" />
						<col class="field-input-column" />
					</colgroup>
				{+END}

				<tbody>
					{FIELDS}
				</tbody>
			</table></div>
		</div>
	</div>
</div>
