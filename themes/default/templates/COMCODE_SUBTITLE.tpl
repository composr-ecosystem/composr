<h{LEVEL%}{+START,IF_PASSED,CLASS} class="{CLASS*}"{+END}>
	{+START,IF_PASSED,ID}<a id="title--{ID*}"></a>{+END}

	{TITLE}
</h{LEVEL%}>
{+START,IF_PASSED,SUB}
	<div class="title-tagline">
		{SUB}
	</div>
{+END}
