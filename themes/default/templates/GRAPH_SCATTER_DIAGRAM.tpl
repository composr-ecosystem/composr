{$REQUIRE_JAVASCRIPT,charts}

<canvas id="chart_{ID%}"{+START,IF_NON_EMPTY,{WIDTH}} width="{WIDTH*}"{+END}{+START,IF_NON_EMPTY,{HEIGHT}} height="{HEIGHT*}"{+END}></canvas>

<script {$CSP_NONCE_HTML}>
	window.addEventListener('load',function () {
		var ctx = document.getElementById('chart_{ID%}').getContext('2d');

		var data = {
			datasets: [{
				data: [
					{+START,LOOP,DATAPOINTS}
						{
							x: {X%},
							y: {Y%},
						},
					{+END}
				],
				pointBackgroundColor: [
					{+START,LOOP,DATAPOINTS}
						'{$?;/,{$IS_NON_EMPTY,{COLOR}},{COLOR},green}',
					{+END}
				],
			}],

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
			legend: {
				display: false,
			},
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
						var tooltip = data.tooltips[tooltipItem.index];
						var ret = '';
						if (tooltip != '') {
							ret += tooltip + ': ';
						}
						ret += '(' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + ')';
						return ret;
					},
				},
			},

			plugins: {
				datalabels: false,
			},
		};

		new Chart(ctx, {
			type: 'scatter',
			data: data,
			options: options,
		});
	});
</script>
