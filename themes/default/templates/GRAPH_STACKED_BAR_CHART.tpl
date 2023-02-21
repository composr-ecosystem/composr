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
						label: '{LABEL;^/}',
						data: [
							{+START,LOOP,DATAPOINTS}
								{VALUE`},
							{+END}
						],
						backgroundColor: [
							{+START,LOOP,DATAPOINTS}
								'{COLOR;^/}',
							{+END}
						],
					},
				{+END}
			],

			labels: [
				{+START,LOOP,LABELS}
					'{LABEL;^/}',
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
					labels: {
					},
					position: 'top',
				},
			{+END}
			scales: {
				xAxes: [{
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{X_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF_PASSED_AND_TRUE,STACKED}
						stacked: true,
					{+END}
					{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
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
					{+START,IF_PASSED_AND_TRUE,STACKED}
						stacked: true,
					{+END}
					{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{LOGARITHMIC}}}
						type: 'logarithmic',
					{+END}
				}],
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						return data.datasets[tooltipItem.datasetIndex].label + ': ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
					},
				},
			},

			plugins: {
				{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
					datalabels: false,
				{+END}
				{+START,IF,{SHOW_DATA_LABELS}}
					datalabels: {
						anchor: 'end',
						align: 'top',
						color: 'black',
						display: function(context) {
							return true;//context.dataset.data[context.dataIndex] > 15;
						},
						font: {
							weight: 'bold'
						},
						formatter: Math.round
					},
				{+END}
			},
		};

		new Chart(ctx, {
			type: '{$?,{HORIZONTAL},horizontalBar,bar}',
			data: data,
			options: options,
		});
	});
</script>
