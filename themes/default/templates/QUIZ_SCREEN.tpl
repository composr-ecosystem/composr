{$REQUIRE_JAVASCRIPT,quizzes}

<div data-tpl="quizScreen" data-tpl-params="{+START,PARAMS_JSON,TIMEOUT}{_*}{+END}">
	{TITLE}

	{+START,IF_NON_EMPTY,{POINTS}}
		<div class="meta-details" role="note">
			<dl class="meta-details-list">
				<dt class="field-name">{!POINTS}:</dt> <dd>{POINTS*}</dd>
			</dl>
		</div>
	{+END}

	{WARNING_DETAILS}

	{$REQUIRE_CSS,quizzes}

	{+START,IF_NON_EMPTY,{START_TEXT}}
		<div class="box box---quiz-screen"><div class="box-inner">
			<div itemprop="description">
				{$PARAGRAPH,{START_TEXT}}
			</div>
		</div></div>
	{+END}

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,quiz,{ID}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

	{+START,IF_NON_EMPTY,{TIMEOUT}}
		<p class="quiz-timer">
			{!TIME_REMAINING,<strong><span id="quiz-timer" style="display: none">{TIMEOUT*}</span><span id="quiz-timer-minutes-and-seconds"></span></strong>}
		</p>
	{+END}

	{$SET,no_required_stars,{ALL_REQUIRED}}
	{+START,IF,{$NOT,{ALL_REQUIRED}}}
		{+START,IF,{$IN_STR,{FIELDS},required-star}}
			{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
		{+END}
	{+END}

	<form title="{!SAVE}" id="quiz-form" class="quiz-form js-quiz-form" method="post" action="{URL*}">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<div class="wide-table-wrap"><table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="quiz-field-name-column" />
						<col class="quiz-field-input-column" />
					</colgroup>
				{+END}

				<tbody>
					{FIELDS}
				</tbody>
			</table></div>

			<p class="proceed-button">
				<button accesskey="u" class="btn btn-primary btn-scr buttons--proceed js-quiz-check-form" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!SUBMIT}</button>
			</p>
		</div>
	</form>

	{$REVIEW_STATUS,quiz,{ID}}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={EDIT_URL*}
		1_TITLE={!EDIT}
		1_ACCESSKEY=q
		1_REL=edit
		1_ICON=admin/edit_this
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=quiz:content_id={ID}:redirect={$SELF_URL&}}
			2_TITLE={!report_content:REPORT_THIS}
			2_ICON=buttons/report
			2_REL=report
		{+END}
	{+END}
</div>
