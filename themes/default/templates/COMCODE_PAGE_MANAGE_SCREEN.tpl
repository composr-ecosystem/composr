<div>
	{TITLE}

	{$PARAGRAPH,{TEXT}}

	{TABLE}

	<form title="{!PRIMARY_PAGE_FORM}" action="{POST_URL*}" method="post">
		{$INSERT_SPAMMER_BLACKHOLE}

		{HIDDEN}

		{+START,IF,{HAS_PAGINATION}}
			<p>
				<label for="filter">{!FILTER}:</label>
				<input type="text" id="filter" class="form-control" name="filter" value="{FILTER*}" data-submit-on-enter="1" />
				<button class="btn btn-primary btn-sm buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
			</p>
		{+END}
	</form>

	{+START,IF,{$NOT,{TRANSLATIONS_MODE}}}
		<h2 class="force_margin">{!COMCODE_PAGE_ADD}</h2>

		<a id="comcode_page_add"></a>

		{+START,IF_PASSED,EXTRA}
			{EXTRA}
		{+END}
		{+START,IF_NON_PASSED,EXTRA}
			<p>{!ACCESS_DENIED}</p>
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{LINKS}}
		<h2>{!ADVANCED}</h2>

		<ul class="actions-list">
			{+START,LOOP,LINKS}
				<li>{+START,INCLUDE,ICON}NAME={LINK_ICON}{+END} <a href="{LINK_URL*}">{LINK_TEXT*}</a></li>
			{+END}
		</ul>
	{+END}
</div>
