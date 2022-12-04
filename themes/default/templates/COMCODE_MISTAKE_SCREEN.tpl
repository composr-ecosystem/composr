{TITLE}

{$REQUIRE_CSS,comcode_mistakes}

<div class="box box---comcode-mistake-screen"><div class="box-inner">
	<h2>{!COMCODE_ERROR_TITLE}</h2>
	{+START,INCLUDE,RED_ALERT}
		ROLE=error
		TEXT={!COMCODE_ERROR,<a href="#errorat" target="_self">{MESSAGE}</a>,{LINE*}}
	{+END}

	<div class="clearfix">
		<div class="comcode-error-help-div">
			<h2>{!WHAT_IS_COMCODE}</h2>

			{!COMCODE_ERROR_HELP_A}
		</div>

		<div class="comcode-error-details-div">
			{+START,IF,{EDITABLE}}
				{FORM}
			{+END}

			<h2>{!ORIGINAL_COMCODE}</h2>

			<table class="map-table wide-table results-table autosized-table">
				<tbody>
					{LINES}
				</tbody>
			</table>
		</div>
	</div>

	<div>
		<h2>{!REPAIR_HELP}</h2>

		<a id="help"></a>

		{!COMCODE_ERROR_HELP_B}
	</div>
</div></div>
