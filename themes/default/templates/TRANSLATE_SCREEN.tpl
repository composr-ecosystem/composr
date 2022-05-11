{TITLE}

{+START,IF,{$NEQ,{LANG},EN}}
	<p>
		{!TRANSLATION_GUIDE,https://www.transifex.com/organization/ocproducts/dashboard/composr-cms-{$VERSION_NUMBER*,1},{LANG},{$TUTORIAL_URL,tut_intl}}
	</p>
{+END}

{+START,IF_NON_EMPTY,{GOOGLE}}
	<p>
		{!POWERED_BY,<a rel="external" title="Google {!LINK_NEW_WINDOW}" target="_blank" href="http://translate.google.com/">Google</a>}.
	</p>
{+END}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div class="wide_table_wrap really_long_table_wrap"><table class="autosized_table columned_table results_table wide_table">
		<thead>
			<tr>
				<th class="translate_line_first">
					{!NAME}/{!DESCRIPTION}
				</th>
				<th>
					{!OLD}/{!NEW}
				</th>
				{+START,IF_NON_EMPTY,{GOOGLE}}
					<th>
						{!ACTIONS}
					</th>
				{+END}
			</tr>
		</thead>
		<tbody>
			{LINES}
		</tbody>
	</table></div>

	<p class="proceed_button">
		<input accesskey="u" onclick="disable_button_just_clicked(this);" class="button_screen buttons__save" type="submit" value="{!SAVE}" />
	</p>
</form>
