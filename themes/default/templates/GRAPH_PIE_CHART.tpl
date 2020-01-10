{$REQUIRE_JAVASCRIPT,charts}

<canvas id="chart_{ID%}"{+START,IF_NON_EMPTY,{WIDTH}} width="{WIDTH*}"{+END}{+START,IF_NON_EMPTY,{HEIGHT}} height="{HEIGHT*}"{+END}></canvas>

<script {$CSP_NONCE_HTML}>
	window.addEventListener('load',function () {
		var ctx = document.getElementById('chart_{ID%}').getContext('2d');

		var data = {
			datasets: [{
				data: [
					{+START,LOOP,DATAPOINTS}
						{VALUE`},
					{+END}
				],
				backgroundColor: [
					{+START,LOOP,DATAPOINTS}
						'{COLOR;/}',
					{+END}
				],
			}],

			labels: [
				{+START,LOOP,DATAPOINTS}
					'{LABEL;/}',
				{+END}
			],
			tooltips: [
				{+START,LOOP,DATAPOINTS}
					'{TOOLTIP;/}',
				{+END}
			],
		};

		var options = {
			{+START,IF_NON_EMPTY,{WIDTH}{HEIGHT}}
				responsive: false,
			{+END}
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data) {
						var tooltip = data.tooltips[tooltipItem.index];
						var ret = '';
						ret += data.labels[tooltipItem.index] + ' = ' + data.datasets[0].data[tooltipItem.index];
						if (tooltip != '') {
							if (ret != '') {
								ret += ': ';
							}
							ret += tooltip;
						}
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
						borderColor: 'white',
						borderRadius: 25,
						borderWidth: 2,
						color: 'white',
						display: function(context) {
							var dataset = context.dataset;
							var count = dataset.data.length;
							var value = dataset.data[context.dataIndex];
							return value > count * 1.5;
						},
						font: {
							weight: 'bold'
						},
						formatter: Math.round,
					},
				{+END}
			},
		};

		new Chart(ctx, {
			type: 'doughnut',
			data: data,
			options: options,
		});
	});
</script>
