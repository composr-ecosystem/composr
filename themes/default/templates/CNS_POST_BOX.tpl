<div class="cns-post-box">
	<div class="wide-table cns-topic">
		{POST}
	</div>

	{+START,IF_PASSED,BREADCRUMBS}
		<nav class="breadcrumbs" itemprop="breadcrumb"><p>
			{!LOCATED_IN,{BREADCRUMBS}}
		</p></nav>

		{+START,IF_PASSED,URL}
			<p class="shunted-button">
				<a class="btn btn-primary btn-scri buttons--more" href="{URL*}" title="{!FORUM_POST} #{ID*}"><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
			</p>
		{+END}
	{+END}
</div>
