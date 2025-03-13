{$REQUIRE_JAVASCRIPT,actionlog}

<div data-tpl="revisionsScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{+START,IF,{INCLUDE_FILTER_FORM}}
		<div class="box box-revisions-screen clearfix"><div class="box-inner">
			<form title="{!PRIMARY_PAGE_FORM}" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" method="get">
				{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,0,0,resource_types=<null>,resource_id=<null>,category_id=<null>,username=<null>}}

				{$REQUIRE_CSS,widget_select2}
				{$REQUIRE_JAVASCRIPT,jquery}
				{$REQUIRE_JAVASCRIPT,select2}
				<div class="revisions-filter-item">
					<label class="lonely-label" for="resource_types">{!TYPE}:</label>
					<select multiple="multiple" name="resource_types" id="resource_types" data-cms-select2="{dropdownAutoWidth: true}">
						{+START,LOOP,RESOURCE_TYPES}
							<option value="{_loop_key*}"{+START,IF_IN_ARRAY,{_loop_key},{$_GET*,resource_types}} selected="selected"{+END}>{_loop_var*}</option>
						{+END}
					</select>
				</div>

				{$REQUIRE_JAVASCRIPT,ajax_people_lists}
				<div class="revisions-filter-item autocomplete-wrapper">
					<label class="lonely-label" for="username">{!USERNAME}:</label>
					<input class="form-control js-focus-update-ajax-member-list js-keyup-update-ajax-member-list" type="text" name="username" id="username" value="{$_GET*,username}" />
				</div>

				<div class="revisions-filter-item">
					<label class="lonely-label" for="resource_id">{!IDENTIFIER} <span class="associated-details">({!ADVANCED})</span>:</label>
					<input type="text" name="resource_id" id="resource_id" class="form-control" value="{$_GET*,resource_id}" />
				</div>

				<div class="revisions-filter-item">
					<label class="lonely-label" for="category_id">{!CATEGORY} <span class="associated-details">({!ADVANCED})</span>:</label>
					<input type="text" name="category_id" id="category_id" class="form-control" value="{$_GET*,category_id}" />
				</div>

				<div class="revisions-filter-item-button">
					<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scri buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
				</div>
			</form>
		</div></div>
	{+END}

	{RESULTS}
</div>
