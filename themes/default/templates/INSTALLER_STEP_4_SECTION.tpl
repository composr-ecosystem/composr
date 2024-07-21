{HIDDEN}

<div class="installer-section">
	<div class="box box---installer-step-4-section"><div class="box-inner">
		<fieldset class="innocuous-fieldset">
			<legend class="accessibility-hidden">{TITLE}</legend>

			<h2>{TITLE}</h2>

			{+START,IF_NON_EMPTY,{TEXT}}
				<p class="associated-details">
					{TEXT}
				</p>
			{+END}

			<table class="map-table form-table wide-table">
				<colgroup>
					<col class="installer-left-column" />
					<col class="installer-right-column" />
				</colgroup>

				<tbody>
					{OPTIONS}
				</tbody>
			</table>
		</fieldset>
	</div></div>
</div>
