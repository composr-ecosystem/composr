<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" data-tpl="installerStep3">
	{HIDDEN}

	<div class="installer-main-min">
		<p>
			{!WELCOME_A,<a title="{!WELCOME_B} {!LINK_NEW_WINDOW}" rel="external" href="{$TUTORIAL_URL*,tut_install}" target="_blank">{!WELCOME_B}</a>}
		</p>

		<p>
			{!FORUM_CHOICE}
		</p>

		<table class="columned-table installer-forums">
			<tbody>
				<tr>
					<th class="de-th">
						<div class="left">
							{!FORUM_SOFTWARE}
						</div>
					</th>

					<th class="de-th">
						<div class="right">
							{!FORUM_VERSION}
						</div>
					</th>
				</tr>
				<tr>
					<td class="installer-forum-list">
						{FORUMS}
					</td>

					<td class="installer-forum-version">
						<div id="versions">
							{VERSION}
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<p style="display: none" id="forum-database-info">
			{!CAREFUL}
		</p>

		{+START,IF,{$NOT,{$GOOGLE_APPENGINE}}}
			<div id="forum-path" style="display: none">
				<p>{!FORUM_PATH_TEXT}</p>
				<table class="map-table form-table wide-table">
					<colgroup>
						<col class="installer-input-left-column" />
						<col class="installer-input-right-column" />
					</colgroup>

					<tbody>
						<tr>
							<th class="form-table-field-name">{!FORUM_PATH}</th>
							<td class="form-table-field-input">
								<div class="accessibility-hidden"><label for="board_path">{!_FORUM_PATH}</label></div>
								<div><input class="form-control form-control-wide" type="text" size="60" id="board_path" name="board_path" value="{FORUM_PATH_DEFAULT*}" /></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="clearfix">
				<p class="lonely-label">
					<a class="toggleable-tray-button js-click-toggle-advanced-db-setup-section" data-tp-section="{!ADVANCED_DATABASE_SETUP~|*}" href="#!">{!ADVANCED_DATABASE_SETUP}</a>
					<a class="toggleable-tray-button js-click-toggle-advanced-db-setup-section" data-tp-section="{!ADVANCED_DATABASE_SETUP~|*}" href="#!"><img class="vertical-alignment" id="img-{!ADVANCED_DATABASE_SETUP|*}" alt="{!EXPAND}: {$STRIP_TAGS,{!ADVANCED_DATABASE_SETUP}}" title="{!EXPAND}" width="24" height="24" src="{$BASE_URL*}/install.php?type=themes/default/images/icons/trays/expand.svg" /></a>
				</p>

				<div id="{!ADVANCED_DATABASE_SETUP|*}" style="display: none">
					<table class="map-table form-table wide-table">
						<colgroup>
							<col class="installer-left-column" />
							<col class="installer-right-column" />
						</colgroup>

						<tbody>
							<tr>
								<th class="form-table-field-name">{!USE_MULTI_DB} <div class="associated-details">{!REQUIRES_MORE_INFO}</div></th>
								<td class="form-table-field-input">
									<label for="yes"><input type="radio" name="use_multi_db" value="1" id="yes" />{!YES}</label>
									<label class="radio-horiz-spacer" for="no"><input type="radio" name="use_multi_db" value="0" id="no" checked="checked" />{!NO}</label>
								</td>
							</tr>

							<tr>
								<th class="form-table-field-name">{!USE_MSN} <div class="associated-details">{!REQUIRES_MORE_INFO}</div></th>
								<td class="form-table-field-input">
									<label for="yes2"><input type="radio" name="use_msn" value="1" id="yes2" />{!YES}</label>
									<label class="radio-horiz-spacer" for="no2"><input type="radio" name="use_msn" value="0" id="no2" checked="checked" />{!NO}</label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		{+END}

		<p>
			<label for="db_type">{!DB_CHOICE}</label>:
			<select id="db_type" name="db_type" class="form-control">
				{DATABASES}
			</select>
		</p>
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" data-disable-on-click="1" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
