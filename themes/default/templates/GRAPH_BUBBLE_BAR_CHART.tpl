{$REQUIRE_JAVASCRIPT,charts}
{$REQUIRE_CSS,graphs}

{$SET,divisor,{$SUBTRACT,{MAX},{MIN}}}
{$SET,base_opacity,0.1}

<div class="bubble-bar-chart-wrap">
	{+START,IF,{$NOT,{$MOBILE}}}
		{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}{TITLE}}
			<div class="float-surrounder">
				{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}}
					<div class="bubble-bar-chart-legend"><div class="bubble-bar-chart-legend-inner">
						<span class="bubble-bar-chart-legend-item"><span class="bubble-bar-chart-legend-item-inner">
							<span class="bubble-bar-chart-legend-label">{MIN*} {Z_AXIS_LABEL*}</span>
							<span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: {$GET*,base_opacity}"></span>
						</span></span>
						<span class="bubble-bar-chart-legend-item"><span class="bubble-bar-chart-legend-item-inner">
							<span class="bubble-bar-chart-legend-label">{MAX*} {Z_AXIS_LABEL*}</span>
							<span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: 1.0"></span>
						</span></span>
					</div></div>
				{+END}

				{+START,IF_NON_EMPTY,{TITLE}}
					<p class="graph-heading">{TITLE*}</p>
				{+END}
			</div>
		{+END}

		<div style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*};{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*};{+END}" class="bubble-bar-chart-sizing">
			<table class="bubble-bar-chart">
				<thead>
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						<tr>
							<th></th>

							<th colspan="{LABELS*}">{X_AXIS_LABEL*}</th>
						</tr>
					{+END}
					<tr>
						<th>{Y_AXIS_LABEL*}</th>

						{+START,LOOP,LABELS}
							<th>{_loop_var*}</th>
						{+END}
					</tr>
				</thead>

				<tbody>
					{+START,LOOP,DATASETS}
						<tr>
							<th {+START,IF_NON_EMPTY,{DATASET_TOOLTIP}}style="cursor: pointer" data-cms-tooltip="{ contents: '{DATASET_TOOLTIP*;^}' }"{+END}>{Y_LABEL*}</th>
							{+START,LOOP,DATAPOINTS}
								<td data-cms-tooltip="{Y_LABEL*}, {LABEL*}: {VALUE*} {$?*,{$EQ,{VALUE},1},{$PREG_REPLACE,s$,,{Z_AXIS_LABEL}},{Z_AXIS_LABEL}}{+START,IF_NON_EMPTY,{TOOLTIP}} &ndash; {TOOLTIP*}{+END}">
									<span style="background-color: {COLOR*}; opacity: {$ADD,{$GET,base_opacity},{$MULT,{$SUBTRACT,1.0,{$GET,base_opacity}},{$DIV_FLOAT,{$SUBTRACT,{VALUE},{MIN}},{$GET,divisor}}}}" class="bubble-bar-chart-cell">
										{+START,IF,{SHOW_DATA_LABELS}}
											{VALUE*}
										{+END}
									</span>
								</td>
							{+END}
						</tr>
					{+END}
				</tbody>
			</table>
		</div>
	{+END}

	{+START,IF,{$MOBILE}}
		{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}{TITLE}}
			<div class="float-surrounder">
				{+START,IF_NON_EMPTY,{TITLE}}
					<p class="graph-heading">{TITLE*}</p>
				{+END}

				{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}}
					<div class="bubble-bar-chart-legend"><div class="bubble-bar-chart-legend-inner">
						<span class="bubble-bar-chart-legend-item"><span class="bubble-bar-chart-legend-item-inner">
							<span class="bubble-bar-chart-legend-label">{MIN*} {Z_AXIS_LABEL*}</span>
							<span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: {$GET*,base_opacity}"></span>
						</span></span>
						<span class="bubble-bar-chart-legend-item"><span class="bubble-bar-chart-legend-item-inner">
							<span class="bubble-bar-chart-legend-label">{MAX*} {Z_AXIS_LABEL*}</span>
							<span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: 1.0"></span>
						</span></span>
					</div></div>
				{+END}
			</div>
		{+END}
		<table class="bubble-bar-chart">
			<thead>
				{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
					<tr>
						<th colspan="{LABELS*}"><h2>{X_AXIS_LABEL*}</h2></th>
					</tr>
				{+END}
				<tr>
					{+START,LOOP,LABELS}
						<th>{_loop_var*}</th>
					{+END}
				</tr>
				{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
					<tr>
						<th colspan="{LABELS*}"><h3>{Y_AXIS_LABEL*}</h3></th>
					</tr>
				{+END}
			</thead>

			<tbody>
				{+START,LOOP,DATASETS}
					<tr>
						<th colspan="{DATAPOINTS*}" class="full-width-label">{Y_LABEL*}</th>
					</tr>
					<tr>
						{+START,LOOP,DATAPOINTS}
							<td data-cms-tooltip="{Y_LABEL*}, {LABEL*}: {VALUE*} {$?*,{$EQ,{VALUE},1},{$PREG_REPLACE,s$,,{Z_AXIS_LABEL}},{Z_AXIS_LABEL}}{+START,IF_NON_EMPTY,{TOOLTIP}} &ndash; {TOOLTIP*}{+END}">
								<span style="background-color: {COLOR*}; opacity: {$ADD,{$GET,base_opacity},{$MULT,{$SUBTRACT,1.0,{$GET,base_opacity}},{$DIV_FLOAT,{$SUBTRACT,{VALUE},{MIN}},{$GET,divisor}}}}" class="bubble-bar-chart-cell">
									{+START,IF,{SHOW_DATA_LABELS}}
										{VALUE*}
									{+END}
								</span>
							</td>
						{+END}
					</tr>
				{+END}
			</tbody>
		</table>
	{+END}
</div>
