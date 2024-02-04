{$REQUIRE_JAVASCRIPT,charts}

<div class="webstandards-checker-off" style="{+START,IF_NON_EMPTY,{WIDTH}}width: {WIDTH*};{+END}{+START,IF_NON_EMPTY,{HEIGHT}}height: {HEIGHT*};{+END}">
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
			{+START,IF,{HORIZONTAL}}
				indexAxis: 'y',
			{+END}
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
				x: {
					{+START,IF_NON_EMPTY,{X_AXIS_LABEL}}
						title: {
							display: true,
							text: '{X_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF,{$AND,{HORIZONTAL},{BEGIN_AT_ZERO}}}
						beginAtZero: true,
					{+END}
					{+START,IF,{$AND,{HORIZONTAL},{CLAMP_Y_AXIS}}}
						max: {MAX%},
					{+END}
					ticks: {
						autoSkip: false,

						{+START,IF,{HORIZONTAL}}
							callback: function(value, index, array) {
								{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
									if ((!isNaN(value)) && (Math.round(Math.log10(value)) != Math.log10(value))) {
										return;
									}
								{+END}

								return value.toLocaleString();
							},
						{+END}
					},
					{+START,IF,{$AND,{HORIZONTAL},{LOGARITHMIC}}}
						type: 'logarithmic',
					{+END}
				},
				y: {
					{+START,IF_NON_EMPTY,{Y_AXIS_LABEL}}
						title: {
							display: true,
							text: '{Y_AXIS_LABEL;^/}',
						},
					{+END}
					{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{BEGIN_AT_ZERO}}}
						beginAtZero: true,
					{+END}
					{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{CLAMP_Y_AXIS}}}
						max: {MAX%},
					{+END}
					ticks: {
						autoSkip: false,

						{+START,IF,{$NOT,{HORIZONTAL}}}
							callback: function(value, index, array) {
								{+START,IF,{$AND,{$NOT,{HORIZONTAL}},{LOGARITHMIC}}}
									if ((!isNaN(value)) && (Math.round(Math.log10(value)) != Math.log10(value))) {
										return;
									}
								{+END}

								return value.toLocaleString();
							},
						{+END}
					},
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
				legend: {
					display: false,
				},
				tooltip: {
					intersect: false,
					callbacks: {
						label: function(tooltipItem) {
							var ret = '';
							{+START,IF,{$NOT,{SHOW_DATA_LABELS}}}
								ret += tooltipItem.dataset.data[tooltipItem.dataIndex].toLocaleString();
							{+END}
							var tooltip = data.tooltips[tooltipItem.dataIndex];
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
							return true;//context.dataset.data[context.dataIndex] > 15;
						},
						font: {
							weight: 'bold',
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
