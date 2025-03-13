{$REQUIRE_JAVASCRIPT,core_themeing}

<div data-view="ThemeManageScreen" data-view-params="{+START,PARAMS_JSON,NAME}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{$REQUIRE_CSS,do_next}

	<h2>{!EXISTING_THEMES}</h2>

	<div class="theme-manage-table">
		<table class="columned-table wide-table">
			<thead>
				<tr>
					<th class="manage-theme-details">{!THEME}</th>
					<th class="manage-theme-export">{!TOOLS}</th>
					<th class="manage-theme-icons">{!EDIT}</th>
				</tr>
			</thead>
			<tbody>
				{$SET,done_one_theme,0}
				{+START,LOOP,THEMES}
					<tr class="{+START,IF,{$GET,done_one_theme}}thick-border{+END}{+START,IF,{IS_MAIN_THEME}} active-item{+END}">
						<td role="note" class="manage-theme-details">
							{+START,IF,{$DESKTOP}}
								<div class="block-desktop">
									{+START,SET,TOOLTIP}
										<kbd>{NAME*}</kbd>, {!BY_SIMPLE,<em>{AUTHOR`}</em>}
										{+START,IF,{$NEQ,{DATE},{!NA_EM}}}
											{DATE*}
										{+END}
									{+END}

									<strong class="comcode-concept-inline" data-cms-tooltip="{$GET*,TOOLTIP}">{TITLE*}</strong>
								</div>
							{+END}
							<div class="block-mobile">
								<p><strong>{TITLE*}</strong></p>
								<p><kbd>{NAME*}</kbd></p>
								<p>{!BY_SIMPLE,<em>{AUTHOR`}</em>}</p>
								{+START,IF,{$NEQ,{DATE},{!NA_EM}}}
									<p>{DATE*}</p>
								{+END}
							</div>
							<dl>
								{+START,IF_PASSED,SEED}
									<dt>{!SEED_COLOUR}:</dt>
									<dd class="seed"><strong style="background: white; color: #{SEED*}">{SEED*}</strong></dd>
								{+END}
							</dl>
						</td>

						<td class="manage-theme-export">
							{+START,IF,{$NEQ,{NAME},default}}
								<p>
									{+START,INCLUDE,ICON}
										NAME=admin/export
										ICON_SIZE=24
									{+END}
									<a data-cms-confirm-click="{!SWITCH_MODULE_WARNING}" href="{$PAGE_LINK*,adminzone:admin_addons:_addon_export:exp=theme:theme={NAME}}">{!addons:EXPORT_THEME}</a>
								</p>
							{+END}
							<p>
								{+START,INCLUDE,ICON}
									NAME=menu/{$DEFAULT_ZONE_PAGE_NAME}
									ICON_SIZE=24
								{+END}
								<a id="theme-preview--{NAME*}" target="_blank" title="{!PREVIEW_THEME} {!LINK_NEW_WINDOW}" href="{$PAGE_LINK*,::keep_theme={NAME}}">{!PREVIEW_THEME}</a>
							</p>
							<p>
								{+START,INCLUDE,ICON}
									NAME=buttons/preview
									ICON_SIZE=24
								{+END}
								<a href="{SCREEN_PREVIEW_URL*}">{!_SCREEN_PREVIEWS}</a>
							</p>
						</td>

						<td class="manage-theme-icons">
							<div class="do-theme-items">
								<div>
									<div>
										<a rel="edit" title="{!EDIT_THEME}: {NAME*}" href="{EDIT_URL*}">{+START,INCLUDE,ICON}
											NAME=admin/edit_this
											ICON_SIZE=48
										{+END}</a>
									</div>
									<div><a title="{!_EDIT_THEME}: {NAME*}" href="{EDIT_URL*}">{$?,{$IS_EMPTY,{THEME_USAGE}},{!EDIT_THEME},{!SETTINGS}}</a></div>
								</div>

								<div>
									<div>
										<a rel="edit" title="{!EDIT_TEMPLATES}: {NAME*}" href="{TEMPLATES_URL*}"{+START,IF,{$EQ,{NAME},default}} data-cms-confirm-click="{!EDIT_DEFAULT_THEME_WARNING}"{+END}>{+START,INCLUDE,ICON}
											NAME=menu/adminzone/style/themes/templates
											ICON_SIZE=48
										{+END}</a>
									</div>
									<div><a title="{!EDIT_TEMPLATES}: {NAME*}" href="{TEMPLATES_URL*}"{+START,IF,{$EQ,{NAME},default}} data-cms-confirm-click="{!EDIT_DEFAULT_THEME_WARNING}"{+END}>{!EDIT_TEMPLATES}</a></div>
								</div>

								<div>
									<div>
										<a rel="edit" title="{!EDIT_THEME_IMAGES}: {NAME*}" href="{IMAGES_URL*}"{+START,IF,{$EQ,{NAME},default}} data-cms-confirm-click="{!EDIT_DEFAULT_THEME_WARNING}"{+END}>{+START,INCLUDE,ICON}
											NAME=menu/adminzone/style/themes/theme_images
											ICON_SIZE=48
										{+END}</a>
									</div>
									<div><a title="{!EDIT_THEME_IMAGES}: {NAME*}" href="{IMAGES_URL*}"{+START,IF,{$EQ,{NAME},default}} data-cms-confirm-click="{!EDIT_DEFAULT_THEME_WARNING}"{+END}>{!EDIT_THEME_IMAGES}</a></div>
								</div>
							</div>
						</td>
					</tr>

					<tr>
						<td colspan="3" class="manage-theme-theme-usage">
							{+START,IF_NON_EMPTY,{THEME_USAGE}}{THEME_USAGE*}{+END}
							{+START,IF,{$EQ,{NAME},default}}
								<p>{!DEFAULT_THEME_INHERITANCE}</p>
							{+END}
						</td>
					</tr>

					{$SET,done_one_theme,1}
				{+END}
			</tbody>
		</table>

		<div class="theme-manage-footnote">
			{+START,IF,{$AND,{$HAS_FORUM,1},{HAS_FREE_CHOICES}}}
				<p><sup>*</sup> {!MEMBERS_MAY_ALTER_THEME}</p>
			{+END}

			{+START,IF_NON_EMPTY,{THEME_DEFAULT_REASON}}
				<p><sup>*</sup> {THEME_DEFAULT_REASON}</p>
			{+END}
		</div>
	</div>

	<h2>{!ADD_THEME}</h2>

	<nav>
		<ul class="actions-list">
			{+START,IF,{$ADDON_INSTALLED,themewizard}}
				<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,adminzone:admin_themewizard:browse}">{!THEMEWIZARD}</a></li>
			{+END}
			<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,adminzone:admin_themes:add_theme}">{!ADD_EMPTY_THEME}</a></li>
		</ul>
	</nav>

	<h2>{!THEME_EXPORT}</h2>

	<div class="box box---theme-manage-screen"><div class="box-inner help-jumpout">
		<p>
			{!IMPORT_EXPORT_THEME_HELP,{$PAGE_LINK*,adminzone:admin_addons:addon_import}}
		</p>
	</div></div>

	{+START,IF,{$GT,{ZONES},6}}
		<h2>{!ZONES}</h2>

		<p class="lonely-label">{!THEMES_AND_ZONES}</p>
		<ul>
			{+START,LOOP,ZONES}
				<li>{_loop_key*} <span class="associated-link"><a title="edit: {!EDIT_ZONE}: {_loop_key*}" data-cms-confirm-click="{!SWITCH_MODULE_WARNING}" href="{$PAGE_LINK*,_SEARCH:admin_zones:_edit:{_loop_key}:redirect={$SELF_URL&}}">{!EDIT}</a></span></li>
			{+END}
		</ul>
	{+END}
</div>
