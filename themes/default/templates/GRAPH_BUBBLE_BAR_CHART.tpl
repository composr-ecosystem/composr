{$REQUIRE_JAVASCRIPT,charts}
{$REQUIRE_CSS,graphs}

{$SET,divisor,{$SUBTRACT,{MAX},{MIN}}}
{$SET,base_opacity,0.1}

<div class="bubble-bar-chart-wrap">
	{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}{TITLE}}
		<div class="float_surrounder">
			{+START,IF_NON_EMPTY,{Z_AXIS_LABEL}}
				<div class="bubble-bar-chart-legend">
					<span>{MIN*} {Z_AXIS_LABEL*}</span> <span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: {$GET*,base_opacity}"></span>
					<span>{MAX*} {Z_AXIS_LABEL*}</span> <span class="bubble-bar-chart-cell" style="background-color: {COLOR*}; opacity: 1.0"></span>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{TITLE}}
				<p class="graph-heading">{TITLE*}</p>
			{+END}
		</div>
	{+END}

	{+START,IF,{$NOT,{$MOBILE}}}
		<div style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*};{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*};{+END}">
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
							<th>{Y_LABEL*}</th>
							{+START,LOOP,DATAPOINTS}
								<td onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{Y_LABEL*;^}, {LABEL*;^}: {VALUE*;^} {$?*,{$EQ,{VALUE},1},{$PREG_REPLACE,s$,,{Z_AXIS_LABEL}},{Z_AXIS_LABEL}}{+START,IF_NON_EMPTY,{TOOLTIP}} &ndash; {TOOLTIP*;^}{+END}');">
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
		<div style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*};{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*};{+END}">
			<table class="bubble-bar-chart">
				<thead>
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						<tr>
							<th colspan="{LABELS*}">{X_AXIS_LABEL*}</th>
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
							<th colspan="{DATAPOINTS*}">{Y_LABEL*}</th>
						</tr>
						<tr>
							{+START,LOOP,DATAPOINTS}
								<td onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{Y_LABEL*;^}, {LABEL*;^}: {VALUE*;^} {$?*,{$EQ,{VALUE},1},{$PREG_REPLACE,s$,,{Z_AXIS_LABEL}},{Z_AXIS_LABEL}}{+START,IF_NON_EMPTY,{TOOLTIP}} &ndash; {TOOLTIP*;^}{+END}');">
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
</div>
