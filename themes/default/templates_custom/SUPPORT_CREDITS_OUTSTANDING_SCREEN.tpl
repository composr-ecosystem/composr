<h2>{TITLE}</h2>

<div class="link-exempt-wrap">
	{DATA}
</div>

{+START,IF_NON_PASSED,NO_SPREADSHEET}
	<p>
		<a href="{$SELF_URL*}&amp;spreadsheet=1" class="xls-link">{!EXPORT_STATS_TO_SPREADSHEET}</a>
	</p>
{+END}
