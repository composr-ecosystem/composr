{$REQUIRE_JAVASCRIPT,charts}

<div class="webstandards-checker-off" style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*}{+START,IF_NON_EMPTY,{HEIGHT}}; {+END}{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*}{+END}">
	<canvas id="chart_{ID%}"></canvas>
</div>

<script {$CSP_NONCE_HTML}>
	window.addEventListener('load',function () {
		var element = document.getElementById('chart_{ID%}');
		var ctx = element.getContext('2d');

		var data = {
			datasets: [{
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
			}],

			labels: [
				{+START,LOOP,DATAPOINTS}
					'{LABEL;^/}',
				{+END}
			],
			tooltips: [
				{+START,LOOP,DATAPOINTS}
					'{TOOLTIP;^/}',
				{+END}
			],
		};

		var options = {
			maintainAspectRatio: (element.parentNode.parentNode.style.display == 'none'), /*Needed for correct sizing in hidden tabs*/
			{+START,IF,{$NOR,{$EQ,{WIDTH},100%},{$IS_EMPTY,{WIDTH}}}}
				responsive: false,
			{+END}
			legend: {
				display: false,
			},
			layout: {
				{+START,IF,{SHOW_DATA_LABELS}}
					{+START,IF,{$NOT,{HORIZONTAL}}}
						padding: {
							left: 5,
							right: 5,
							top: 25,
							bottom: 5,
						},
					{+END}
					{+START,IF,{HORIZONTAL}}
						padding: {
							left: 5,
							right: 25,
							top: 5,
							bottom: 5,
						},
					{+END}
				{+END}
				{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
					padding: {
						left: 5,
						right: 5,
						top: 5,
						bottom: 5,
					},
				{+END}
			},
			scales: {
				{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
					xAxes: [{
						scaleLabel: {
							display: true,
							labelString: '{X_AXIS_LABEL;^/}',
						},
						ticks: {
						{+START,IF,{$AND,{HORIZONTAL},{BEGIN_AT_ZERO}}}
							beginAtZero: true,
						{+END}
						autoSkip: false,
						{+START,IF,{$AND,{HORIZONTAL},{CLAMP_Y_AXIS}}}
							max: {MAX%},
						{+END}
						},
						{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
							type: 'logarithmic',
						{+END}
					}],
				{+END}
				yAxes: [{
					{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
						scaleLabel: {
							display: true,
							labelString: '{Y_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF,{BEGIN_AT_ZERO}}
						ticks: {
						{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{BEGIN_AT_ZERO}}}
							beginAtZero: true,
						{+END}
						autoSkip: false,
						{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{CLAMP_Y_AXIS}}}
							max: {MAX%},
						{+END}
						},
					{+END}
					{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{LOGARITHMIC}}}
						type: 'logarithmic',
					{+END}
				}],
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						var ret = '';
						{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
							ret += data.datasets[0].data[tooltipItem.index];
						{+END}
						var tooltip = data.tooltips[tooltipItem.index];
						if (tooltip != '') {
							if (ret != '') {
								ret += ': ';
							}
							ret += tooltip;
						}

						return ret.split("\n");
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
						{+START,IF,{HORIZONTAL}}
							align: 'end',
						{+END}
						{+START,IF,{$NOT,{HORIZONTAL}}}
							align: 'top',
						{+END}
						color: 'black',
						display: function(context) {
							return context.dataset.data[context.dataIndex] > 0;
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
