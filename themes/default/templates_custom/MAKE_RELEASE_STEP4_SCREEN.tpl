{TITLE}

{$PARAGRAPH,{TEXT*}}

{$,pre-process Transifex}
{$SET,transifex,{!MAKE_RELEASE_STEP4_TRANSIFEX_OPTIONAL,{$FIND_SCRIPT*,transifex_push},{$FIND_SCRIPT*,transifex_pull}}}
{+START,IF,{IS_SUBSTANTIAL}}{+START,IF,{$NOT,{IS_BLEEDING_EDGE}}}
	{$SET,transifex,{!MAKE_RELEASE_STEP4_TRANSIFEX}}
{+END}{+END}

<ol>
	{$,cleaning up}
	<li>{!MAKE_RELEASE_STEP4_DB}</li>

	{$,Transifex}
	<li>{$GET,transifex}</li>

	{$,addons part 1}
	<li>
		{!MAKE_RELEASE_STEP4_ADDONS}
		<ul>
			{+START,IF,{IS_SUBSTANTIAL}}{+START,IF,{$NOT,{IS_BLEEDING_EDGE}}}
				<li>{!MAKE_RELEASE_STEP4_ADDONS_UPDATE_VERSION,{NEW_VERSION_FLOAT.}}</li>
			{+END}{+END}
			<li>{!MAKE_RELEASE_STEP4_ADDONS_GENERATE,{$PAGE_LINK,_SEARCH:build_addons}}</li>\
			<li>{!MAKE_RELEASE_STEP4_ADDONS_UPLOAD}</li>
		</ul>
	</li>

	{$,publishing the build 1}
	<li>{!MAKE_RELEASE_STEP4_UPLOAD,{COMMAND_TO_TRY*},{NEW_VERSION_DOTTED*}}</li>
	<li>{!MAKE_RELEASE_STEP4_TAG,{$REPLACE*, ,-,{NEW_VERSION_DOTTED}}}</li>

	{$,update homesite}
	<li>
		{!MAKE_RELEASE_STEP4_HOMESITE,{BRAND_DOMAIN*}}
		<ul>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_BRANCH}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_MERGE,{$VERSION_BRANCH_NAME*}}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_CHECK,{BRAND_DOMAIN*}}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_GIT_PUSH}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_CLOSE}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_GIT_PULL}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_CHECK2}</li>
			<li>{!MAKE_RELEASE_STEP4_HOMESITE_OPEN}</li>
		</ul>
	</li>

	{$,addons and publishing the build part 2}
	<li>
		<form target="_blank" style="display: inline" action="{PUSH_URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<input type="hidden" name="version" value="{NEW_VERSION_DOTTED*}" />
			<input type="hidden" name="is_bleeding_edge" value="{IS_BLEEDING_EDGE*}" />
			<input type="hidden" name="is_old_tree" value="{IS_OLD_TREE*}" />
			<input type="hidden" name="descrip" value="{DESCRIPTION*}" />
			<input type="hidden" name="needed" value="{NEEDED*}" />
			<input type="hidden" name="criteria" value="{CRITERIA*}" />
			<input type="hidden" name="justification" value="{JUSTIFICATION*}" />
			<input type="hidden" name="db_upgrade" value="{DB_UPGRADE*}" />
			<input type="hidden" name="video_url" value="{VIDEO_URL*}" />
			<input type="hidden" name="changes" value="{CHANGES*}" />
			<button class="btn btn-primary btn-scr" type="submit">{!MAKE_RELEASE_STEP4_PUBLISH}</button>
		</form>
	</li>
	<li>{!MAKE_RELEASE_STEP4_TEST,{$BRAND_BASE_URL*}}</li>
	<li>{!MAKE_RELEASE_STEP4_ADDONS_PUBLISH,{$BRAND_BASE_URL*},{NEW_VERSION_FLOAT.},{NEW_VERSION_BRANCH.}}</li>

	{$,API}
	<li>{!MAKE_RELEASE_STEP4_API,{$BRAND_BASE_URL*}}</li>

	{$,third party integrations}
	{+START,IF,{$NOT,{IS_BLEEDING_EDGE}}}{+START,IF,{$NOT,{IS_OLD_TREE}}}
		{!MAKE_RELEASE_STEP4_INTEGRATIONS}
	{+END}{+END}

	{$,key-pairs}
	{+START,IF,{IS_SUBSTANTIAL}}
		<li>{!MAKE_RELEASE_STEP4_KEY_PAIRS}</li>
	{+END}

	{+START,IF,{IS_SUBSTANTIAL}}{+START,IF,{$NOT,{IS_BLEEDING_EDGE}}}
		{$,tracker}
		<li>{!MAKE_RELEASE_STEP4_TRACKER,{TRACKER_URL*},{PROJECT_ID*}}</li>

		{$,documentation}
		<li>
			{!MAKE_RELEASE_STEP4_DOCUMENTATION}
			<ul>
				<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_INDEX,{$PAGE_LINK,_SEARCH:doc-index-build}}</li>
				<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_GIT}</li>
				<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_ZONE,{$BRAND_BASE_URL*},{NEW_VERSION_MAJOR*}}</li>
				<li>
					{!MAKE_RELEASE_STEP4_DOCUMENTATION_COMMANDS,{BRAND_DOMAIN*}}
					<ul>
						<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_COMMANDS_RM,{NEW_VERSION_MAJOR*}}</li>
						<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_COMMANDS_CP,{NEW_VERSION_MAJOR*}}</li>
						<li>{!MAKE_RELEASE_STEP4_DOCUMENTATION_COMMANDS_SYMLINK,{NEW_VERSION_MAJOR*}}</li>
					</ul>
				</li>
			</ul>
		</li>

		{$,ERD}
		<li>
			{!MAKE_RELEASE_STEP4_ERD}
			<ul>
				<li>{!MAKE_RELEASE_STEP4_ERD_WORKBENCH}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_SQL,{$PAGE_LINK,_SEARCH:sql-schema-generate-by-addon}}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_DIRECTORY}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_IMPORT}</li>
				<li>
					{!MAKE_RELEASE_STEP4_ERD_IMPORT_EACH}
					<ul>
						<li>{!MAKE_RELEASE_STEP4_ERD_IMPORT_REVERSE_ENGINEER}</li>
						<li>{!MAKE_RELEASE_STEP4_ERD_IMPORT_ARRANGEMENT}</li>
						<li>{!MAKE_RELEASE_STEP4_ERD_IMPORT_GRAPHIC}</li>
					</ul>
				</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_ZIP}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_DOCS}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_TABLE_DETAILS,{$PAGE_LINK,_SEARCH:sql-show-tables-by-addon}}</li>
				<li>{!MAKE_RELEASE_STEP4_ERD_GIT}</li>
			</ul>
		</li>

		{$,addons}
		<li>
			{!MAKE_RELEASE_STEP4_ADDONS}
			<ul>
				<li>{!MAKE_RELEASE_STEP4_ADDONS_GENERATE_HOMESITE,{$BRAND_BASE_URL*}}</li>
				<li>{!MAKE_RELEASE_STEP4_ADDONS_PUBLISH,{$BRAND_BASE_URL*},{NEW_VERSION_FLOAT.},{NEW_VERSION_BRANCH.}}</li>
			</ul>
		</li>

		{$,history}
		<li>{!MAKE_RELEASE_STEP4_HISTORY,{BRAND_DOMAIN*}}</li>

		{$,Wikipedia forum post}
		<li>{!MAKE_RELEASE_STEP4_WIKIPEDIA_SUBSTANTIAL,{$BRAND_BASE_URL*},{NEW_VERSION_FLOAT*}}</li>

		{$,syndication}
		<li>
			{!MAKE_RELEASE_STEP4_SYNDICATION,{TRACKER_URL*}}
			<ul>
				{!MAKE_RELEASE_STEP4_SYNDICATION_SITES}
			</ul>
		</li>

		{$,newsletter}
		<li>{!MAKE_RELEASE_STEP4_NEWSLETTER,{$BRAND_BASE_URL*}}</li>
	{+END}{+END}

	{+START,IF,{IS_SUBSTANTIAL}}{+START,IF,{IS_BLEEDING_EDGE}}
		<li>{!MAKE_RELEASE_STEP4_VIP}</li>
	{+END}{+END}
</ol>
