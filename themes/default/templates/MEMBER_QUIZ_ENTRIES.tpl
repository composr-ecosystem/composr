{+START,IF_NON_EMPTY,{CATEGORIES}}
	<div data-toggleable-tray="{}">
		<h2 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<span class="js-tray-onclick-toggle-tray">{!TEST_RESULTS}</span>
		</h2>

		<div class="toggleable-tray js-tray-content" style="display: none">
			{+START,LOOP,CATEGORIES}
				{$,If more than one category of quiz entered then show headers}
				{+START,IF,{$OR,{$GET,want_sections},{$NEQ,{CATEGORIES},1}}}
					<h3>{_loop_key*}</h3>
				{+END}

				{+START,IF_NON_EMPTY,{DELETE_URL}}
				<form action="{DELETE_URL*}" method="post" title="{!QUIZ}: {!DELETE}">
					{$INSERT_FORM_POST_SECURITY}
				{+END}
				<table class="columned-table wide-table results-table autosized-table responsive-table">
					<thead>
						<tr>
							<th>
								{!NAME}
							</th>

							<th>
								{!DATE}
							</th>

							<th>
								{!MARKS}
							</th>

							<th>
								{!PERCENTAGE}
							</th>

							<th>
								{!STATUS}
							</th>

							{+START,IF_NON_EMPTY,{DELETE_URL}}
								<th>
									{!DELETE}
								</th>
							{+END}
						</tr>
					</thead>

					<tbody>
						{+START,LOOP,QUIZZES}
							<tr>
								<td>
									<a data-cms-tooltip="{QUIZ_START_TEXT*}" href="{QUIZ_URL*}">{QUIZ_NAME*}</a>
								</td>

								<td>
									{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_quiz,adminzone}}
										<a href="{$PAGE_LINK*,adminzone:admin_quiz:__quiz_results:{ENTRY_ID}}">{ENTRY_DATE*}</a>
									{+END}

									{+START,IF,{$NOT,{$HAS_ACTUAL_PAGE_ACCESS,admin_quiz,adminzone}}}
										{ENTRY_DATE*}
									{+END}
								</td>

								<td>
									{MARKS_RANGE*} / {OUT_OF*}
								</td>

								<td>
									{PERCENTAGE_RANGE*}%
								</td>

								<td>
									{+START,IF_PASSED,PASSED}
										{+START,IF,{PASSED}}
											<span class="multilist-mark yes">{!PASSED}</span>
										{+END}

										{+START,IF,{$NOT,{PASSED}}}
											<span class="multilist-mark no">{!FAILED}</span>
										{+END}
									{+END}

									{+START,IF_NON_PASSED,PASSED}
										{!UNKNOWN}
									{+END}
								</td>

								{+START,IF_NON_EMPTY,{DELETE_URL}}
									<td>
										<label for="delete-quiz-{ENTRY_ID*}">{!DELETE}: {QUIZ_NAME*}</label>
										<input type="checkbox" id="delete-quiz-{ENTRY_ID*}" name="delete_{ENTRY_ID*}" value="1" />
									</td>
								{+END}
							</tr>
						{+END}
					</tbody>

					{+START,IF_NON_EMPTY,{DELETE_URL}}
						<tfoot>
							<tr>
								<td colspan="5"></td>

								<td>
									<button class="btn btn-danger btn-scri" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE}</span></button>
								</td>
							</tr>
						</tfoot>
					{+END}
				</table>
				{+START,IF_NON_EMPTY,{DELETE_URL}}
				</form>
				{+END}

				<br />{SORTING}

				{$,If more than one category of quiz entered then show summaries}
				{+START,IF,{$OR,{$GET,want_sections},{$NEQ,{CATEGORIES},1}}}
					<p class="lonely-label">
						{!RESULT_OVERVIEW}:
					</p>

					<dl class="compact-list">
						<dt>{!MARKS}</dt>
						<dd>{$FLOAT_FORMAT*,{RUNNING_MARKS},2,1}/{RUNNING_OUT_OF*}</dd>

						<dt>{!PERCENTAGE}</dt>
						<dd>{$FLOAT_FORMAT*,{RUNNING_PERCENTAGE},2,1}%</dd>
					</dl>
				{+END}
			{+END}
		</div>
	</div>
{+END}
