{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<p>{CATEGORY_DESCRIPTION*}</p>

<h2>{!CONTENTS}</h2>

<ul>
	{+START,LOOP,_GROUPS}
		<li><a href="#group_{_loop_key*}">{_loop_var}</a></li>
	{+END}
</ul>

<h2>{!OPTION_GROUPS}</h2>

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		{+START,LOOP,GROUPS}
			<a id="group_{GROUP_NAME*}"></a>

			<h3>{GROUP_TITLE*}</h3>

			<div class="wide_table_wrap"><table class="map_table form_table wide_table">
				{+START,IF,{$NOT,{$MOBILE}}}
					<colgroup>
						<col class="field_name_column" />
						<col class="field_input_column" />
					</colgroup>
				{+END}

				<tbody>
					{+START,IF_NON_EMPTY,{GROUP_DESCRIPTION}}
						<tr class="form_table_field_spacer">
							<th{+START,IF,{$NOT,{$MOBILE}}} colspan="2"{+END} class="table_heading_cell">
								{GROUP_DESCRIPTION*}
							</th>
						</tr>
					{+END}

					{GROUP}
				</tbody>
			</table></div>
		{+END}
	</div>

	{+START,INCLUDE,FORM_STANDARD_END}{+END}
</form>

