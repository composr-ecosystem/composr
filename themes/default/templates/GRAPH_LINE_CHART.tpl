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
						label: '{LABEL;/}',
						fill: false,
						backgroundColor: '{COLOR;/}',
						borderColor: '{COLOR;/}',
						data: [
							{+START,LOOP,DATAPOINTS}
								{VALUE`},
							{+END}
						],
						tooltips: [
							{+START,LOOP,DATAPOINTS}
								'{TOOLTIP;/}',
							{+END}
						],
					},
				{+END}
			],

			labels : [
				{+START,LOOP,X_LABELS}
					'{_loop_var;/}',
				{+END}
			],
		};

		var options = {
			{+START,IF_NON_EMPTY,{WIDTH}{HEIGHT}}
				responsive: true,
				maintainAspectRatio: false,
			{+END}
			{+START,IF,{$EQ,{DATASETS},1}}
				legend: {
					display: false,
				},
			{+END}
			scales: {
				xAxes: [{
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{X_AXIS_LABEL;/}',
						},
					{+END}
					{+START,IF_IN_ARRAY,X_LABELS,}{$,If blank labels have been placed we can assume this is to space things out manually}
						ticks: {
							autoSkip: false,
						},
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
						if (tooltip != '') {
							ret += tooltip;
							ret += ': ';
						}
						ret += tooltipItem.yLabel;
						return ret;
					},
				},
			},

			plugins: {
				{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
					datalabels: false,
				{+END}
				{+START,IF,{SHOW_DATA_LABELS}}
					datalabels: {
						backgroundColor: function(context) {
							return context.dataset.backgroundColor;
						},
						borderRadius: 4,
						color: '#FFFFFF',
						font: {
							weight: 'bold'
						},
					},
				{+END}
			},
		};

		new Chart(ctx, {
			type: 'line',
			data: data,
			options: options,
		});
	});
</script>
