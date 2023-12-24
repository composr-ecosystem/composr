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
			{+START,IF,{HORIZONTAL}}
				indexAxis: 'y',
			{+END}
			scales: {
				x: {
					{+START,IF,{$NAND,{HORIZONTAL},{LOGARITHMIC}}}
						{+START,IF_PASSED_AND_TRUE,STACKED}
							border: {
								display: false,
							},
						{+END}
					{+END}
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						title: {
							display: true,
							text: '{X_AXIS_LABEL;^/}',
						},
					{+END}
					ticks: {
						{+START,IF,{HORIZONTAL}}
							{+START,IF_PASSED_AND_TRUE,STACKED}
								callback: function(value, index, array) {
									return '';
								},
							{+END}
							{+START,IF_NON_PASSED_OR_FALSE,STACKED}
								callback: function(value, index, array) {
									{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
										if ((!isNaN(value)) && (Math.round(Math.log10(value)) != Math.log10(value))) {
											return;
										}
									{+END}

									return value.toLocaleString();
								},
							{+END}
						{+END}
					},

					{+START,IF_PASSED_AND_TRUE,STACKED}
						stacked: true,
					{+END}

					{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
						type: 'logarithmic',
					{+END}
				},
				y: {
					{+START,IF,{$NAND,{$NOT,{HORIZONTAL}},{LOGARITHMIC}}}
						border: {
							{+START,IF_PASSED_AND_TRUE,STACKED}
								display: false,
							{+END}
						},
					{+END}
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
						{+START,IF,{$NOT,{HORIZONTAL}}}
							{+START,IF_PASSED_AND_TRUE,STACKED}
								callback: function(value, index, array) {
									return '';
								},
							{+END}
							{+START,IF_NON_PASSED_OR_FALSE,STACKED}
								callback: function(value, index, array) {
									{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
										if ((!isNaN(value)) && (Math.round(Math.log10(value)) != Math.log10(value))) {
											return;
										}
									{+END}

									return value.toLocaleString();
								},
							{+END}
						{+END}
					},
					{+START,IF_PASSED_AND_TRUE,STACKED}
						stacked: true,
					{+END}

					{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{LOGARITHMIC}}}
						type: 'logarithmic',
					{+END}
				},
			},

			interaction: {
            mode: 'index',
				{+START,IF,{HORIZONTAL}}
            	axis: 'y',
				{+END}
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
							return tooltipItem.dataset.label + ': ' + tooltipItem.dataset.data[tooltipItem.dataIndex].toLocaleString();
						},
					},
				},
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
						formatter: function(value, context) {
							return value.toLocaleString();
						},
					},
				{+END}
			},
		};

		new Chart(ctx, {
			type: 'bar',
			data: data,
			options: options,
		});
	});
</script>
