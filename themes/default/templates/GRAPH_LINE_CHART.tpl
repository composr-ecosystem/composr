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
						fill: {$?,{FILL},'origin',false},
						backgroundColor: '{COLOR;^/}',
						borderColor: '{COLOR;^/}',
						data: [
							{+START,LOOP,DATAPOINTS}
								{VALUE`},
							{+END}
						],
						tooltips: [
							{+START,LOOP,DATAPOINTS}
								'{TOOLTIP;^/}',
							{+END}
						],
					},
				{+END}
			],

			labels : [
				{+START,LOOP,X_LABELS}
					'{_loop_var;^/}',
				{+END}
			],
		};

		var options = {
			tension: 1,
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
					{+START,IF,{$LT,{DATASETS},3}}
						position: 'top',
					{+END}
					{+START,IF,{$GT,{DATASETS},2}}
						position: 'right',
					{+END}
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
						if (tooltip != '') {
							ret += tooltip;
						}

						{+START,IF,{$NEQ,{DATASETS},1}}
							if (ret != '') {
								ret += ': ';
							}
							ret += data.datasets[tooltipItem.datasetIndex].label;
						{+END}

						{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
							if (ret != '') {
								ret += ': ';
							}
							ret += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
						{+END}

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
						backgroundColor: function(context) {
							return context.dataset.backgroundColor;
						},
						borderRadius: 4,
						font: {
							weight: 'bold'
						},
						color: 'white',
						formatter: Math.round
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
