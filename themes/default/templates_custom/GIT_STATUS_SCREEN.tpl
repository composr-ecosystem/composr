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

<div class="meta-details" role="note">
	<ul class="meta-details-list">
		<li><strong>Branch</strong>: <kbd>{BRANCH*}</kbd></li>
		<li><strong>Unsynched local commits</strong>: {NUM_UNSYNCHED_LOCAL_COMMITS*}</li>
		<li><strong>Unsynched remote commits</strong>: {NUM_UNSYNCHED_REMOTE_COMMITS*}</li>
	</ul>
</div>

<div data-view="GitStatus">
	<form id="git_status_form" action="{$SELF_URL*,0,0,0,type=action,include_ignored={INCLUDE_IGNORED}}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<input type="hidden" name="git_action" value="" />

		<h2>Local files (pending push to remote)</h2>

		<table class="columned-table wide-table results-table">
			<colgroup>
				<col style="width: 60%" />
				<col style="width: 40%" />
				<col style="width: 75px" />
				<col style="width: 200px" />
				<col style="width: 100px" />
				<col style="width: 35px" />
			</colgroup>

			<thead>
				<th>File path</th>
				<th>Actions</th>
				<th>File size</th>
				<th>Last modified</th>
				<th>Status</th>
				<th></th>
			</head>

			<tbody>
				{+START,IF,{$NOT,{HAS_LOCAL_FILES}}}
					<tr>
						<td colspan="6"><p class="nothing-here">{!NO_ENTRIES}</p></td>
					</tr>
				{+END}
				{+START,IF,{HAS_LOCAL_FILES}}
					{+START,LOOP,LOCAL_FILES}
						<tr>
							<td>
								{DIRECTORY*}{+START,IF_NON_EMPTY,{DIRECTORY}}/{+END}<strong>{$TRUNCATE_LEFT,{FILENAME},50}</strong>
							</td>
							<td>
								{+START,IF,{$EQ,{_GIT_STATUS},{$GIT_STATUS__MODIFIED}}}
									<a href="{$PAGE_LINK*,_SELF:_SELF:local_diff:{PATH&}}" target="_blank" title="View diff for {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">{$TRUNCATE_LEFT,{FILENAME},50}</a>
								{+END}
								{+START,IF,{$NEQ,{_GIT_STATUS},{$GIT_STATUS__MODIFIED}}}
									{$TRUNCATE_LEFT,{FILENAME},50}
								{+END}

								<ul class="horizontal-links">
									{+START,IF,{$NEQ,{_GIT_STATUS},{$GIT_STATUS__DELETED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:local_view:{PATH_ENCODED&}}" target="_blank" title="View {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">View</a></li>
									{+END}
									{+START,IF,{$EQ,{_GIT_STATUS},{$GIT_STATUS__DELETED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:remote_view:{PATH_ENCODED&}}" target="_blank" title="View {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">View</a></li>
									{+END}

									{+START,IF,{$EQ,{_GIT_STATUS},{$GIT_STATUS__MODIFIED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:local_diff:{PATH_ENCODED&}}" target="_blank" title="View diff for {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">Changes</a></li>
									{+END}
								</ul>
							</td>

							<td>
								{+START,IF_PASSED,FILE_SIZE}
									{FILE_SIZE*}
								{+END}
								{+START,IF_NON_PASSED,FILE_SIZE}
									<em>Unknown</em>
								{+END}
							</td>
							<td>
								{+START,IF_PASSED,MTIME}
									{MTIME*}
								{+END}
								{+START,IF_NON_PASSED,MTIME}
									<em>Unknown</em>
								{+END}
							</td>

							<td>{GIT_STATUS*}</td>

							<td>
								<label for="local_select_{PATH_HASH*}" class="accessibility-hidden">Select {PATH*}</label>
								<input type="checkbox" name="local_select_{PATH_HASH*}" id="local_select_{PATH_HASH*}" value="{PATH*}" class="js-btn-refresh-file-selected" />
							</td>
						</tr>
					{+END}
				{+END}

				<tr>
					<td colspan="6">
						{+START,IF,{$NOT,{INCLUDE_IGNORED}}}
							<button id="button_refresh_with_ignored" class="button-screen-item buttons--all js-btn-refresh-with-ignored" type="submit">{+START,INCLUDE,ICON}NAME=buttons/all{+END} Refresh with ignored files</button>
						{+END}
						{+START,IF,{INCLUDE_IGNORED}}
							<button id="button_refresh_without_ignored" class="button-screen-item buttons--all2 js-btn-refresh-without-ignored" type="submit">{+START,INCLUDE,ICON}NAME=buttons/all2{+END} Refresh without ignored files</button>
						{+END}

						<button id="button_local_shell_paths" class="button-screen-item buttons--calculate js-btn-show-local-shell-paths" type="button" disabled="disabled">{+START,INCLUDE,ICON}NAME=buttons/calculate{+END} Shell paths</button>

						<button id="button_local_tar" class="button-screen-item buttons--save js-btn-download-local-tar" type="submit" disabled="disabled">{+START,INCLUDE,ICON}NAME=buttons/save{+END} Download TAR</button>

						<button id="button_push" class="button-screen-item buttons--upload js-btn-push" type="submit" disabled="disabled">{+START,INCLUDE,ICON}NAME=buttons/upload{+END} Commit &amp; Push</button>

						<button id="button_revert" class="button-screen-item menu--admin--delete js-btn-delete-local-changes" type="submit" disabled="disabled">{+START,INCLUDE,ICON}NAME=admin/delete{+END} Delete/revert files</button>

						<button style="float: right" class="button-screen-item buttons--choose js-git-local-select-all" type="button">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} Select all</button>
						<button style="float: right" class="button-screen-item buttons--choose js-git-local-select-none" type="button">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} Select none</button>
					</td>
				</tr>
			</tbody>
		</table>

		<h2>Remote files</h2>

		<table class="columned-table wide-table results-table">
			<colgroup>
				<col style="width: 60%" />
				<col style="width: 40%" />
				<col style="width: 75px" />
				<col style="width: 200px" />
				<col style="width: 100px" />
				<col style="width: 35px" />
			</colgroup>

			<thead>
				<th>File path</th>
				<th>Actions</th>
				<th>File size</th>
				<th>Last modified</th>
				<th>Status</th>
				<th></th>
			</head>

			<tbody>
				{+START,IF,{$NOT,{HAS_REMOTE_FILES}}}
					<tr>
						<td colspan="6"><p class="nothing-here">{!NO_ENTRIES}</p></td>
					</tr>
				{+END}
				{+START,IF,{HAS_REMOTE_FILES}}
					{+START,LOOP,REMOTE_FILES}
						<tr>
							<td>
								{DIRECTORY*}{+START,IF_NON_EMPTY,{DIRECTORY}}/{+END}<strong>{$TRUNCATE_LEFT,{FILENAME},50}</strong>
							</td>
							<td>
								<ul class="horizontal-links">
									{+START,IF,{$NEQ,{_GIT_STATUS},{$GIT_STATUS__DELETED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:remote_view:{PATH_ENCODED&}}" target="_blank" title="View {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">View</a></li>
									{+END}
									{+START,IF,{$EQ,{_GIT_STATUS},{$GIT_STATUS__DELETED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:local_view:{PATH_ENCODED&}}" target="_blank" title="View {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">View</a></li>
									{+END}

									{+START,IF,{$EQ,{_GIT_STATUS},{$GIT_STATUS__MODIFIED}}}
										<li><a href="{$PAGE_LINK*,_SELF:_SELF:remote_diff:{PATH_ENCODED&}}" target="_blank" title="View diff for {PATH*} {!LINK_NEW_WINDOW}" data-open-as-overlay="{}">Changes</a></li>
									{+END}
								</ul>
							</td>

							<td>
								{+START,IF_PASSED,FILE_SIZE}
									{FILE_SIZE*}
								{+END}
								{+START,IF_NON_PASSED,FILE_SIZE}
									<em>Unknown</em>
								{+END}
							</td>
							<td>
								{+START,IF_PASSED,MTIME}
									{MTIME*}
								{+END}
								{+START,IF_NON_PASSED,MTIME}
									<em>Unknown</em>
								{+END}
							</td>

							<td>
								{GIT_STATUS*}
							</td>

							<td>
								<label for="remote_select_{PATH_HASH*}" class="accessibility-hidden">Select {PATH*}</label>
								<input type="checkbox" name="remote_select_{PATH_HASH*}" id="remote_select_{PATH_HASH*}" value="{PATH*}" data-open-as-overlay="{}"{+START,IF,{$NOT,{EXISTS_LOCALLY}}} disabled="disabled"{+END} />
							</td>
						</tr>
					{+END}

					{+START,IF,{HAS_MAX_REMOTE_FILES}}
						<tr>
							<td colspan="6">
								<p class="nothing-here">Too many files to show</p>
							</td>
						</tr>
					{+END}
				{+END}

				<tr>
					<td colspan="6">
						<button id="button_remote_shell_paths" class="button-screen-item buttons--calculate js-btn-show-remote-shell-paths" type="button" disabled="disabled">{+START,INCLUDE,ICON}NAME=buttons/calculate{+END} Shell paths</button>

						<button id="button_remote_tar" class="button-screen-item buttons--save js-btn-download-remote-tar" type="submit" disabled="disabled">{+START,INCLUDE,ICON}NAME=buttons/save{+END} Download TAR (to backup what would be overwritten)</button>

						<button id="button_pull" class="button-screen-item buttons--copy js-btn-pull" type="submit">{+START,INCLUDE,ICON}NAME=buttons/copy{+END} Pull</button>

						<button style="float: right" class="button-screen-item buttons--choose js-git-remote-select-all" type="button">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} Select all</button>
						<button style="float: right" class="button-screen-item buttons--choose js-git-remote-select-none" type="button">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} Select none</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

<form action="{$URL_FOR_GET_FORM*,{$SELF_URL,0,0,0,type=browse,sort=<null>,include_ignored={INCLUDE_IGNORED}}}" method="get">
	{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,0,0,sort=<null>,include_ignored=<null>}}

	<p>
		<label for="sort">Sort by</label>:
		<select name="sort" id="sort">
			<option {+START,IF,{$EQ,{SORT},path ASC}} selected="selected"{+END} value="path ASC">Path (A-Z)</option>
			<option {+START,IF,{$EQ,{SORT},path DESC}} selected="selected"{+END} value="path DESC">Path (Z-A)</option>
			<option {+START,IF,{$EQ,{SORT},file_size ASC}} selected="selected"{+END} value="file_size ASC">File size (small to big)</option>
			<option {+START,IF,{$EQ,{SORT},file_size DESC}} selected="selected"{+END} value="file_size DESC">File size (big to small)</option>
			<option {+START,IF,{$EQ,{SORT},mtime ASC}} selected="selected"{+END} value="mtime ASC">Last modified (oldest first)</option>
			<option {+START,IF,{$EQ,{SORT},mtime DESC}} selected="selected"{+END} value="mtime DESC">Last modified (newest first)</option>
			<option {+START,IF,{$EQ,{SORT},git_status ASC}} selected="selected"{+END} value="git_status ASC">Git status</option>
		</select>
		<button type="submit" class="button-micro buttons--sort">{+START,INCLUDE,ICON}NAME=buttons/sort{+END} {!SORT}</button>
	</p>
</form>
