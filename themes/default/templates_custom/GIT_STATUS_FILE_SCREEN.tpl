{TITLE}

{+START,IF_PASSED,MEDIA_CURRENT}
	{+START,IF_PASSED,MEDIA_BEFORE}
		<h2>Before</h2>

		{MEDIA_BEFORE}

		<h2>After</h2>
	{+END}

	{MEDIA_CURRENT}
{+END}

{+START,IF_PASSED,DIFF}
	<div class="git-status-diff">
		{DIFF`}
	</div>
{+END}
