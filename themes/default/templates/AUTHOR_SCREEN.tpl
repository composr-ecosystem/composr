<div itemscope="itemscope" itemtype="http://schema.org/ProfilePage">
	{TITLE}

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<div class="box box---author-screen--description">
			<div class="box-inner">
				<h2>{!AUTHOR_ABOUT}</h2>

				<div itemprop="description" class="clearfix">
					{DESCRIPTION}
				</div>
			</div>
		</div>
	{+END}

	{+START,IF,{$NOT,{$VALUE_OPTION,hide_author_skills}}}
		{+START,IF_NON_EMPTY,{SKILLS}}
			<div class="box box---author-screen-skills">
				<div class="box-inner">
					<h2>{!SKILLS}</h2>

					{SKILLS}
				</div>
			</div>
		{+END}
	{+END}

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,author,{AUTHOR}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

	{+START,IF_NON_EMPTY,{URL_DETAILS}{FORUM_DETAILS}{POINT_DETAILS}}
		<div class="box box---author-screen--functions">
			<div class="box-inner">
				<p>
					{!AUTHOR_FUNCTIONS,{AUTHOR*}}&hellip;
				</p>
				<nav>
					<ul class="actions-list-compact" itemprop="significantLinks">
						{URL_DETAILS}
						{FORUM_DETAILS}
						{POINT_DETAILS}
						{SEARCH_DETAILS}
					</ul>
				</nav>
			</div>
		</div>
	{+END}

	{+START,IF_EMPTY,{DESCRIPTION}{SKILLS}{URL_DETAILS}{FORUM_DETAILS}{POINT_DETAILS}}
		<p>{!AUTHOR_NULL}</p>
	{+END}

	{+START,IF,{$ADDON_INSTALLED,downloads}}
		<div class="box box---author-screen--downloads">
			<div class="box-inner">
				<p>
					{!DOWNLOADS_RELEASED}&hellip;
				</p>
				{DOWNLOADS_RELEASED}
				{+START,IF_EMPTY,{DOWNLOADS_RELEASED}}
					<p class="nothing-here">{!NO_DOWNLOADS_FOUND}</p>
				{+END}
			</div>
		</div>
	{+END}

	{+START,IF,{$ADDON_INSTALLED,news}}
		<div class="box box---author-screen--news">
			<div class="box-inner">
				<p>
					{!NEWS_RELEASED}&hellip;
				</p>
				{NEWS_RELEASED}
				{+START,IF_EMPTY,{NEWS_RELEASED}}
					<p class="nothing-here">{!NONE}</p>
				{+END}
			</div>
		</div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{$REVIEW_STATUS,author,{AUTHOR}}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={EDIT_URL*}
		1_TITLE={!EDIT}
		1_ACCESSKEY=q
		1_REL=edit
		1_ICON=admin/edit_this
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=author:content_id={AUTHOR}:redirect={$SELF_URL&}}
			2_TITLE={!report_content:REPORT_THIS}
			2_ICON=buttons/report
			2_REL=report
		{+END}
	{+END}
</div>
