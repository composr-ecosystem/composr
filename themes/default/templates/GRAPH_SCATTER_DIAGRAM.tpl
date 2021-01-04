{$REQUIRE_JAVASCRIPT,charts}

<div class="webstandards-checker-off" style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*}{+START,IF_NON_EMPTY,{HEIGHT}}; {+END}{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*}{+END}">
	<canvas id="chart_{ID%}"></canvas>
</div>

<script {$CSP_NONCE_HTML}>
	window.addEventListener('load',function () {
		var ctx = document.getElementById('chart_{ID%}').getContext('2d');

		var data = {
			datasets: [
				{+START,LOOP,DATASETS}
					{
						data: [
							{+START,LOOP,DATAPOINTS}
								{
									x: {X},
									y: {Y},
									{+START,IF_PASSED,R}
										r: {R},
									{+END}
								},
							{+END}
						],
						{+START,IF_NON_EMPTY,{CATEGORY}}
							label: '{CATEGORY;/}',
						{+END}
						backgroundColor: '{COLOR;/}',

						tooltips: [
							{+START,LOOP,DATAPOINTS}
								'{TOOLTIP;/}',
							{+END}
						],
					},
				{+END}
			],
		};

		var options = {
			{+START,IF_NON_EMPTY,{WIDTH}{HEIGHT}}
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
							labelString: '{X_AXIS_LABEL;/}',
						}
					{+END}
				}],
				yAxes: [{
					{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{Y_AXIS_LABEL;/}',
						},
					{+END}
					{+START,IF,{BEGIN_AT_ZERO}}
						ticks: {
							beginAtZero: true,
						},
					{+END}
				}],
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						var tooltip = data.datasets[tooltipItem.datasetIndex].tooltips[tooltipItem.index];
						var ret = '';
						if (tooltip) {
							ret += tooltip + ': ';
						}
						ret += '(' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + ')';
						return ret;
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
