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

{+START,IF_EMPTY,{EXTRA}}
	<p>{!IMPORT_WARNING}</p>
{+END}

{+START,IF_NON_EMPTY,{EXTRA}}
	<ul>
		{EXTRA}
	</ul>
{+END}

{$REQUIRE_CSS,forms}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	{HIDDEN}

	<p>{!SELECT_TO_IMPORT}</p>
	<table class="map-table form-table wide-table import-actions">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				<col class="field-name-column" />
				<col class="field-input-column" />
			</colgroup>
		{+END}

		<tbody>
			{IMPORT_LIST}
		</tbody>
	</table>

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!IMPORT}</button>
	</p>
</form>

{+START,IF_NON_EMPTY,{MESSAGE}}
	<hr class="spaced-rule" />

	<section class="box"><div class="box-inner">
		<p>{MESSAGE*}</p>
	</div></section>
{+END}
