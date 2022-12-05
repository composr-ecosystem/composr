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
			legend: false,
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						var ret = '';
						ret += data.labels[tooltipItem.index] + ' = ' + data.datasets[0].data[tooltipItem.index];
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
						backgroundColor: function(context) {
							return context.dataset.backgroundColor;
						},
						color: 'white',
						borderRadius: 25,
						borderWidth: 1,
						display: function(context) {
							var dataset = context.dataset;
							var count = dataset.data.length;
							var value = dataset.data[context.dataIndex];
							return value > count * 1.5;
						},
						font: {
							weight: 'bold'
						},
						formatter: function(value, context) {
							return context.chart.data.labels[context.dataIndex];
						}
					},
				{+END}
			},
		};

		new Chart(ctx, {
			type: '{$?,{DOUGHNUT},doughnut,pie}',
			data: data,
			options: options,
		});
	});
</script>
