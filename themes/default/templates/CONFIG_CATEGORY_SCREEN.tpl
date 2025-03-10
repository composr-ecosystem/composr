{$REQUIRE_JAVASCRIPT,core_configuration}

{TITLE}

{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<p>{CATEGORY_DESCRIPTION*}</p>

<h2>{!CONTENTS}</h2>

<ul>
	{+START,LOOP,_GROUPS}
		<li><a href="#group-{_loop_key*}">{_loop_var}</a></li>
	{+END}
</ul>

<h2>{!OPTION_GROUPS}</h2>

<form title="{!PRIMARY_PAGE_FORM}" data-submit-modsecurity-workaround="1" action="{URL*}" method="post" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}{+END}">
	{$INSERT_FORM_POST_SECURITY}

	<div>
		{+START,LOOP,GROUPS}
			<a id="group-{GROUP_NAME*}"></a>

			<h3>{GROUP_TITLE*}</h3>

			<table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="field-name-column" />
						<col class="field-input-column" />
					</colgroup>
				{+END}

				<tbody>
					{+START,IF_NON_EMPTY,{GROUP_DESCRIPTION}}
						<tr class="form-table-field-spacer">
							<th colspan="2" class="table-heading-cell">
								{GROUP_DESCRIPTION*}
							</th>
						</tr>
					{+END}

					{GROUP}
				</tbody>
			</table>
		{+END}
	</div>

	{+START,INCLUDE,FORM_STANDARD_END}{+END}
</form>
