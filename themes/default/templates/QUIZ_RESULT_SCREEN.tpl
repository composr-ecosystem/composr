{TITLE}

<p class="quiz-result-headline">
	{!QUIZ_WAS_ENTERED_AS_FOLLOWS,{USERNAME*},{MEMBER_URL*},{DATE*},{QUIZ_NAME*},{TYPE*}}
</p>

{+START,INCLUDE,QUIZ_RESULTS}{+END}

{+START,IF,{$NEQ,{_TYPE},SURVEY}}
	<p class="lonely-label">
		{!QUIZ_RESULTS}:
	</p>

	<dl class="compact-list">
		<dt>{!MARKS}</dt>
		<dd>{MARKS_RANGE*} / {OUT_OF*}</dd>

		<dt>{!PERCENTAGE}</dt>
		<dd>{PERCENTAGE_RANGE*}%</dd>

		{+START,IF,{$EQ,{_TYPE},TEST}}
			<dt>{!STATUS}</dt>
			<dd>
				{+START,IF_PASSED,PASSED}
					{+START,IF,{PASSED}}
						<span class="multilist-mark yes" title="{!PASSED}">&#10003;</span> {$,Checkmark entity}
					{+END}

					{+START,IF,{$NOT,{PASSED}}}
						<span class="multilist-mark no" title="{!FAILED}">&#10007;</span> {$,Cross entity}
					{+END}
				{+END}

				{+START,IF_NON_PASSED,PASSED}
					{!UNKNOWN}
				{+END}
			</dd>
		{+END}
	</dl>
{+END}
