{$REQUIRE_JAVASCRIPT,core_language_editing}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{+START,IF,{$NEQ,{LANG},EN}}
	<p>
		{!TRANSLATION_GUIDE,https://www.transifex.com/organization/ocproducts/dashboard/composr-cms-{$VERSION_NUMBER*,1},{LANG},{$TUTORIAL_URL,tut_intl}}
	</p>
{+END}

{+START,IF_NON_EMPTY,{TRANSLATION_CREDIT}}
	<p>
		{TRANSLATION_CREDIT}
	</p>
{+END}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" data-submit-modsecurity-workaround="1">
	{$INSERT_FORM_POST_SECURITY}

	<div class="really-long-table-wrap">
		<table class="autosized-table columned-table results-table wide-table responsive-table">
			<thead>
				<tr>
					<th class="translate-line-first">
						{!CODENAME}
					</th>
					<th>
						{!OLD}/{!NEW}
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
		<button disabled="disabled" id="translate-button" accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
	</p>
</form>

{+START,IF_NON_EMPTY,{PAGINATION}}
	<div class="js-translate-pagination clearfix pagination-spacing">
		{PAGINATION}
	</div>
{+END}
