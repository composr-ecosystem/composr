{$REQUIRE_JAVASCRIPT,charts}

<div class="webstandards-checker-off" style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*}{+START,IF_NON_EMPTY,{HEIGHT}}; {+END}{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*}{+END}">
	<canvas id="chart_{ID%}"></canvas>
</div>

<script {$CSP_NONCE_HTML}>
	window.addEventListener('load',function () {
		var element = document.getElementById('chart_{ID%}');
		var ctx = element.getContext('2d');

		var data = {
			datasets: [
				{+START,LOOP,DATASETS}
					{
						data: [
							{+START,LOOP,DATAPOINTS}
								{
									x: {X/},
									y: {Y/},
									{+START,IF_PASSED,R}
										r: {R/},
									{+END}
								},
							{+END}
						],
						{+START,IF_NON_EMPTY,{CATEGORY}}
							label: '{CATEGORY;^/}',
						{+END}
						backgroundColor: '{COLOR;^/}',

						tooltips: [
							{+START,LOOP,DATAPOINTS}
								'{TOOLTIP;^/}',
							{+END}
						],
					},
				{+END}
			],
		};

		var options = {
			maintainAspectRatio: (element.parentNode.parentNode.style.display == 'none'), /*Needed for correct sizing in hidden tabs*/
			{+START,IF,{$NOR,{$EQ,{WIDTH},100%},{$IS_EMPTY,{WIDTH}}}}
				responsive: false,
			{+END}
			{+START,IF,{$EQ,{DATASETS},1}}
				legend: {
					display: false,
				},
			{+END}
			{+START,IF,{$NEQ,{DATASETS},1}}
				legend: {
					display: true,
					position: 'right',
				},
			{+END}
			scales: {
				xAxes: [{
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{X_AXIS_LABEL;^/}',
						}
					{+END}
					{+START,IF,{LOGARITHMIC}}
						type: 'logarithmic',
					{+END}
				}],
				yAxes: [{
					{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{Y_AXIS_LABEL;^/}',
						},
					{+END}
					ticks: {
						{+START,IF,{BEGIN_AT_ZERO}}
							beginAtZero: true,
						{+END}
						{+START,IF,{CLAMP_Y_AXIS}}
							max: {MAX%},
						{+END}
					},
					{+START,IF,{LOGARITHMIC}}
						type: 'logarithmic',
					{+END}
				}],
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						var ret = '';
						var tooltip = data.datasets[tooltipItem.datasetIndex].tooltips[tooltipItem.index];
						if (tooltip) {
							ret += tooltip + ': ';
						}
						ret += '(' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + ')';
						return ret.split("\n");
					},
					mode: 'dataset',
				},
			},

			plugins: {
				datalabels: false,
			},
		};

		new Chart(ctx, {
			type: '{$?,{BUBBLE},bubble,scatter}',
			data: data,
			options: options,
		});
	});
</script>
