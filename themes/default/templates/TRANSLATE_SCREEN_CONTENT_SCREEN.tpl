{$REQUIRE_JAVASCRIPT,core_language_editing}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{!TRANSLATE_CONTENT_SCREEN,{LANG_NICE_NAME*}}

{+START,IF_NON_EMPTY,{TRANSLATION_CREDIT}}
	<p>
		{TRANSLATION_CREDIT}
	</p>
{+END}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	<div class="really-long-table-wrap">
		<table class="columned-table results-table wide-table autosized-table responsive-table">
			<thead>
				<tr>
					<th>
						{!CODENAME}
					</th>
					<th>
						{!ORIGINAL}{+START,IF,{$NEQ,{LANG_ORIGINAL_NAME},{LANG}}} ({LANG_NICE_ORIGINAL_NAME*}?){+END}
						&rarr;
						{LANG_NICE_NAME*}
					</th>
					{+START,IF_NON_EMPTY,{TRANSLATION_CREDIT}}
						<th>
							{!ACTIONS}
						</th>
					{+END}
				</tr>
			</thead>

			<tbody>
				{LINES}
			</tbody>
		</table>
	</div>

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
	</p>

	{+START,IF,{TOO_MANY}}
		<p class="more-here">{!TRANSLATE_TOO_MANY,{TOTAL*},{MAX*}}</p>
	{+END}
</form>

{PAGINATION}
