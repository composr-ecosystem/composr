<div data-view="AddonScreen">
	{TITLE}

	<p>{!ADDONS_SCREEN}</p>

	{+START,IF_NON_EMPTY,{UPDATED_ADDONS}}
		{$,Link repeated here and below because it is important}
		<nav>
			<ul class="actions-list spaced-list">
				<li>
					{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SELF:_SELF:addon_import:to_import={UPDATED_ADDONS}}"><strong>{!IMPORT_UPDATED_ADDONS}</strong></a>
				</li>
			</ul>
		</nav>
	{+END}

	<div class="clearfix">
		<p class="right associated-link">
			<a href="#!" class="js-click-check-uninstall-all">{!UNINSTALL}: {!USE_ALL}</a>
		</p>
	</div>

	<form title="{!PRIMARY_PAGE_FORM}" action="{MULTI_ACTION*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<input type="hidden" name="csrf_token_preserve" value="1" />

		<div class="not-too-tall-addons">
			<table class="columned-table wide-table results-table autosized-table zebra responsive-table">
				<thead>
					<tr>
						<th>
							{!NAME}
						</th>
						<th>
							{!AUTHOR}
						</th>
						<th>
							{!VERSION}
						</th>
						<th>
							{!STATUS}
						</th>
						<th class="column-mobile">
							{!DESCRIPTION}
						</th>
						<th class="column-mobile">
							{!FILES}
						</th>
						<th>
							{!ACTIONS}
						</th>
					</tr>
				</thead>

				<tbody>
					{+START,LOOP,ADDON_STRUCTURE}
						<tr>
							<th colspan="7" class="table-heading-cell vertical-alignment">
								{$,Category}
								<span class="h2">{_loop_key*}</span>
							</th>
						</tr>

						{+START,LOOP,COLOURS}
							{+START,LOOP,ADDONS}
								{_loop_var}
							{+END}
						{+END}
					{+END}
				</tbody>
			</table>
		</div>

		<p class="proceed-button">
			<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{!INSTALL_AND_UNINSTALL} {+START,INCLUDE,ICON}NAME=buttons/proceed{+END}</button>
		</p>
	</form>

	<h2>{!ACTIONS}</h2>

	<nav>
		<ul class="actions-list spaced-list">
			{+START,IF_NON_EMPTY,{UPDATED_ADDONS}}
				<li>
					{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SELF:_SELF:addon_import:to_import={UPDATED_ADDONS}}">{!IMPORT_UPDATED_ADDONS}</a>
				</li>
			{+END}
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SELF:_SELF:addon_import}">{!IMPORT_ADDON}</a> ({!IMPORT_ADDON_2})
			</li>
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SELF:_SELF:addon_export}">{!EXPORT_ADDON_TITLE}</a>
			</li>
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} {!ADVANCED}: <a href="{$PAGE_LINK*,_SELF:_SELF:modules}">{!MODULE_MANAGEMENT}</a>
			</li>
		</ul>
	</nav>
</div>
