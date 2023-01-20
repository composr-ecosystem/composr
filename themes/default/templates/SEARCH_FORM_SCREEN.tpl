{$REQUIRE_JAVASCRIPT,search}

<div data-view="SearchFormScreen" data-view-params="{+START,PARAMS_JSON,SEARCH_TYPE}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,RESULTS}
		{+START,IF_EMPTY,{RESULTS}}
			<p class="nothing-here">{!NO_RESULTS_SEARCH}</p>
		{+END}
		{+START,IF_NON_EMPTY,{RESULTS}}
			<h2>{$?,{$IS_EMPTY,{SEARCH_TERM}},{!SEARCH_RESULTS_ARE_UNNAMED,{NUM_RESULTS*}{$?,{$EQ,{NUM_RESULTS},{$INTEGER_FORMAT,{$MAXIMUM_RESULT_COUNT_POINT}}},+}},{!SEARCH_RESULTS_ARE,{NUM_RESULTS*}{$?,{$EQ,{NUM_RESULTS},{$INTEGER_FORMAT,{$MAXIMUM_RESULT_COUNT_POINT}}},+},{SEARCH_TERM*}}}</h2>

			<div class="clearfix" itemscope="itemscope" itemtype="http://schema.org/SearchResultsPage">
				{RESULTS}
			</div>

			{+START,IF_NON_EMPTY,{PAGINATION}}
				<div class="clearfix pagination-spacing">
					{PAGINATION}
				</div>
			{+END}
		{+END}
	{+END}

	{+START,IF_PASSED,RESULTS}
	<div class="box" data-toggleable-tray="{}">
	<div class="box-inner">
	{+END}
		{+START,IF_PASSED,RESULTS}
			<h2 class="toggleable-tray-title">
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!SETTINGS}</a>
				{+START,IF_NON_EMPTY,{RESULTS}}
					<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!SHOW_SEARCH_FORM}">{+START,INCLUDE,ICON}
						NAME=trays/expand
						ICON_SIZE=24
					{+END}</a>
				{+END}
			</h2>
		{+END}

		<div id="search-form" class="toggleable-tray js-tray-content"{+START,IF_PASSED,RESULTS}{+START,IF_NON_EMPTY,{RESULTS}} style="display: none"{+END}{+END}>
			<form title="{!PRIMARY_PAGE_FORM}" action="{$URL_FOR_GET_FORM*,{URL}}" target="_self" method="get" class="main-search-form js-search-form" role="search">
				{$HIDDENS_FOR_GET_FORM,{URL}}
				<input type="hidden" name="all_defaults" value="0" />

				<table class="map-table form-table wide-table">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="field-name-column" />
							<col class="field-input-column" />
							<col class="field-sup-column" />
						</colgroup>
					{+END}

					<tbody>
						<tr>
							<th class="form-table-field-name">{!SEARCH_FOR}</th>
							<td class="form-table-field-input" colspan="2">
								<div class="accessibility-hidden"><label for="search-content">{!SEARCH_FOR}</label></div>
								<div class="autocomplete-wrapper">
									<input maxlength="255"{+START,IF,{$MOBILE}} autocorrect="off"{+END} class="search-content form-control form-control-wide js-keyup-update-ajax-search-list" type="search" size="{$?,{$MOBILE},30,48}" id="search-content" name="content" value="{+START,IF_PASSED,CONTENT}{CONTENT*}{+END}" />
								</div>

								{+START,IF,{HAS_TEMPLATE_SEARCH}}
									<p class="associated-details">{!MAY_LEAVE_BLANK_ADVANCED}</p>
								{+END}
							</td>
						</tr>
						<tr>
							<th class="form-table-field-name">{USER_LABEL*}</th>
							<td class="form-table-field-input" colspan="2">
								<div class="accessibility-hidden"><label for="search-author">{USER_LABEL*}</label></div>
								<div>
									<span class="invisible-ref-point"></span>
									<input maxlength="80" class="form-control form-control-wide js-keyup-update-author-list" type="text" value="{AUTHOR*}" id="search-author" name="author"{+START,IF,{$MOBILE}} autocorrect="off"{+END} />
								</div>
							</td>
						</tr>
						{+START,IF_PASSED,DAYS_LABEL}
							<tr>
								<th class="form-table-field-name">{DAYS_LABEL*}</th>
								<td class="form-table-field-input" colspan="2">
									<div class="accessibility-hidden"><label for="search-days">{DAYS_LABEL*}</label></div>
									<select id="search-days" name="days" class="form-control">
										<option selected="selected" value="-1">{!NA}</option>
										<option {+START,IF,{$EQ,{DAYS},2}} selected="selected"{+END} value="2">{!SUBMIT_AGE_DAYS,2}</option>
										<option {+START,IF,{$EQ,{DAYS},5}} selected="selected"{+END} value="5">{!SUBMIT_AGE_DAYS,5}</option>
										<option {+START,IF,{$EQ,{DAYS},15}} selected="selected"{+END} value="15">{!SUBMIT_AGE_DAYS,15}</option>
										<option {+START,IF,{$EQ,{DAYS},30}} selected="selected"{+END} value="30">{!SUBMIT_AGE_DAYS,30}</option>
										<option {+START,IF,{$EQ,{DAYS},45}} selected="selected"{+END} value="45">{!SUBMIT_AGE_DAYS,45}</option>
										<option {+START,IF,{$EQ,{DAYS},60}} selected="selected"{+END} value="60">{!SUBMIT_AGE_DAYS,60}</option>
										<option {+START,IF,{$EQ,{DAYS},120}} selected="selected"{+END} value="120">{!SUBMIT_AGE_DAYS,120}</option>
										<option {+START,IF,{$EQ,{DAYS},240}} selected="selected"{+END} value="240">{!SUBMIT_AGE_DAYS,240}</option>
										<option {+START,IF,{$EQ,{DAYS},365}} selected="selected"{+END} value="365">{!SUBMIT_AGE_DAYS,365}</option>
									</select>
								</td>
							</tr>
						{+END}
						{+START,IF_PASSED,DATE_RANGE_LABEL}
							<tr>
								<th class="form-table-field-name">{DATE_RANGE_LABEL*}</th>
								<td class="form-table-field-input" colspan="2">
									<div class="accessibility-hidden"><label for="cutoff_from">{DATE_RANGE_LABEL*} {!FROM}</label></div>
									{+START,INCLUDE,FORM_SCREEN_INPUT_DATE}
										NAME=cutoff_from
										TYPE=date
										REQUIRED=0
										UNLIMITED=0
										DAY={CUTOFF_FROM_DAY}
										MONTH={CUTOFF_FROM_MONTH}
										YEAR={CUTOFF_FROM_YEAR}
										MIN_DATE_DAY=1
										MIN_DATE_MONTH=1
										MIN_DATE_YEAR=1970
										MAX_DATE_DAY=
										MAX_DATE_MONTH=
										MAX_DATE_YEAR=
										READ_ONLY=0
									{+END}
									<div class="accessibility-hidden"><label for="cutoff_to">{DATE_RANGE_LABEL*} {!TO}</label></div>
									{+START,INCLUDE,FORM_SCREEN_INPUT_DATE}
										NAME=cutoff_to
										TYPE=date
										REQUIRED=0
										UNLIMITED=0
										DAY={CUTOFF_TO_DAY}
										MONTH={CUTOFF_TO_MONTH}
										YEAR={CUTOFF_TO_YEAR}
										MIN_DATE_DAY=1
										MIN_DATE_MONTH=1
										MIN_DATE_YEAR=1970
										MAX_DATE_DAY=
										MAX_DATE_MONTH=
										MAX_DATE_YEAR=
										READ_ONLY=0
									{+END}
								</td>
							</tr>
						{+END}
						<tr>
							<th class="form-table-field-name">{!SORT}</th>
							<td class="form-table-field-input" colspan="2">
								<div class="accessibility-hidden"><label for="search-direction">{!DIRECTION}</label></div>
								<div class="accessibility-hidden"><label for="search-sort">{!SORT_BY}</label></div>
								<select id="search-sort" name="sort" class="form-control">
									<option {+START,IF,{$EQ,{SORT},relevance}} selected="selected"{+END} value="relevance">{!RELEVANCE_SORT}</option>
									<option {+START,IF,{$EQ,{SORT},add_date}} selected="selected"{+END} value="add_date">{!DATE}</option>
									<option {+START,IF,{$EQ,{SORT},title}} selected="selected"{+END} value="title">{!TITLE}</option>
									<option {+START,IF,{$EQ,{SORT},rating}} selected="selected"{+END} value="average_rating">{!RATING}</option>
									<option {+START,IF,{$EQ,{SORT},rating}} selected="selected"{+END} value="compound_rating">{!POPULARITY}</option>
									{+START,LOOP,EXTRA_SORT_FIELDS}
										<option {+START,IF,{$EQ,{SORT},{_loop_key*}}} selected="selected"{+END} value="{_loop_key*}">{_loop_var*}</option>
									{+END}
								</select>
								<select id="search-direction" name="direction" class="form-control">
									<option {+START,IF,{$EQ,{DIRECTION},ASC}} selected="selected"{+END} value="ASC">{!ASCENDING}</option>
									<option {+START,IF,{$EQ,{DIRECTION},DESC}} selected="selected"{+END} value="DESC">{!DESCENDING}</option>
								</select>
							</td>
						</tr>

						{SPECIALISATION}
					</tbody>
				</table>

				<p class="proceed-button">
					<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scr buttons--search" type="submit">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {!SEARCH_TITLE}</button>
				</p>

				{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
				{+START,IF,{$AND,{$SHOW_DOCS},{$HAS_PRIVILEGE,see_software_docs}}}
					{+START,INCLUDE,STAFF_ACTIONS}
						STAFF_ACTIONS_TITLE={!STAFF_ACTIONS}
						1_URL={$TUTORIAL_URL*,tut_search}
						1_TITLE={!HELP}
						1_REL=help
						1_ICON=help
					{+END}
				{+END}
			</form>
		</div>
	{+START,IF_PASSED,RESULTS}
	</div>
	</div>
	{+END}
</div>
