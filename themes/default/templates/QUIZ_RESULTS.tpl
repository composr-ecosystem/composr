{$,This is shown to the quiz member if reveal answers is on. Otherwise it is just shown to staff and the member just sees an explanation list instead.}

<table class="columned-table results-table wide-table autosized-table responsive-table">
	<colgroup>
		<col class="quiz-done-results-col-was-correct" />
		<col class="quiz-done-results-col-question" />
		<col class="quiz-done-results-col-given-answer" />
		<col class="quiz-done-results-col-correct-answer" />
	</colgroup>

	<thead>
		<tr>
			<th></th>
			<th>{!QUESTION}</th>
			<th>{!GIVEN_ANSWER}</th>
			<th>{!CORRECT_ANSWER}</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,GIVEN_ANSWERS_ARR}
			{$SET,cycle,{$CYCLE,results_table_zebra,zebra-0,zebra-1}}

			<tr class="{$GET,cycle} thick-border">
				<td class="quiz-answer-status">
					{+START,IF_PASSED,WAS_CORRECT}
						{+START,IF,{WAS_CORRECT}}
							<span class="multilist-mark yes">&#10004;</span>
						{+END}
						{+START,IF,{$NOT,{WAS_CORRECT}}}
							<span class="multilist-mark no">&#10005;</span>
						{+END}
					{+END}
					{+START,IF_NON_PASSED,WAS_CORRECT}
						&ndash;
					{+END}
				</td>

				<td class="quiz-result-question">
					{QUESTION}
				</td>

				<td class="quiz-result-given-answer">
					{$COMCODE,{GIVEN_ANSWER},0}
				</td>

				<td class="quiz-result-answer">
					{+START,IF_NON_EMPTY,{CORRECT_ANSWER}}
						{$COMCODE,{CORRECT_ANSWER},0}
					{+END}

					{+START,IF_EMPTY,{CORRECT_ANSWER}}
						<em>{!MANUALLY_MARKED}</em>
					{+END}
				</td>
			</tr>

			{+START,IF_PASSED,CORRECT_EXPLANATION}{+START,IF_NON_EMPTY,{CORRECT_EXPLANATION}}
				<tr class="{$GET,cycle}">
					<td class="responsive-table-no-prefix" colspan="4">
						<span class="field-name">{!EXPLANATION}:</span> {$COMCODE,{CORRECT_EXPLANATION},0}
					</td>
				</tr>
			{+END}{+END}
		{+END}
	</tbody>
</table>
