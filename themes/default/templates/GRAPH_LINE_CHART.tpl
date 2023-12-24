{$REQUIRE_JAVASCRIPT,charts}

<div class="webstandards-checker-off" style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*};{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*};{+END}">
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
			cubicInterpolationMode: 'monotone',
			maintainAspectRatio: (element.parentNode.parentNode.style.display == 'none'), /*Needed for correct sizing in hidden tabs*/
			scales: {
				x: {
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						title: {
							display: true,
							text: '{X_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF_IN_ARRAY,X_LABELS,}{$,If blank labels have been placed we can assume this is to space things out manually}
						ticks: {
							autoSkip: false,
						},
					{+END}
				},
				y: {
					{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
						title: {
							display: true,
							text: '{Y_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF,{BEGIN_AT_ZERO}}
						beginAtZero: true,
					{+END}
					{+START,IF,{CLAMP_Y_AXIS}}
						max: {MAX%},
					{+END}
					ticks: {
						callback: function(value, index, array) {
							{+START,IF,{LOGARITHMIC}}
								if ((!isNaN(value)) && (Math.round(Math.log10(value)) != Math.log10(value))) {
									return;
								}
							{+END}

							return value.toLocaleString();
						},
					},

					{+START,IF,{LOGARITHMIC}}
						type: 'logarithmic',
					{+END}
				},
			},

			interaction: {
            mode: 'x',
			},

			plugins: {
				{+START,IF,{$EQ,{DATASETS},1}}
					legend: {
						display: false,
					},
				{+END}
				{+START,IF,{$NEQ,{DATASETS},1}}
					legend: {
						display: true,
						position: 'top',
					},
				{+END}
				tooltip: {
					intersect: false,
					callbacks: {
						label: function(tooltipItem) {
							var ret = '';

							var tooltip = tooltipItem.dataset.tooltips[tooltipItem.dataIndex];
							if (tooltip != '') {
								ret += tooltip;
							}

							{+START,IF,{$NEQ,{DATASETS},1}}
								if (ret != '') {
									ret += ': ';
								}
								ret += tooltipItem.dataset.label;
							{+END}

							{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
								if (ret != '') {
									ret += ': ';
								}
								ret += tooltipItem.dataset.data[tooltipItem.dataIndex].toLocaleString();
							{+END}

							return ret.split("\n");
						},
					},
				},
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
						formatter: function(value, context) {
							return value.toLocaleString();
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
