{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	{+START,IF_NON_EMPTY,{ZONES}}
		<h2>{!ZONES} &amp; {!PAGES}</h2>

		<div class="content-access-table-wrap">
			<table class="columned-table results-table responsive-table content-access-table" style="width: {$ADD*,20,{$MULT,5,1}}em">
				<colgroup>
					<col class="content-access-table-label" />
					<col class="content-access-table-setting" />
				</colgroup>

				<thead>
					<tr>
						<th>
							{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{!zones:ZONE} / {!PAGE},UL_ESCAPED}{$KEEP}}
							<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{!zones:ZONE} / {!PAGE}" alt="{!zones:ZONE} / {!PAGE}" />
						</th>
						<th>
							{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{!VIEW},UL_ESCAPED}{$KEEP}}
							<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{!VIEW}" alt="{!VIEW}" />
						</th>
					</tr>
				</thead>

				<tbody>
					{+START,LOOP,ZONES}
						<tr>
							<td>
								{ZONE_TITLE*}
							</td>
							<td>
								{+START,INCLUDE,PERMISSIONS_CONTENT_ACCESS_TICK}
									LABEL={ZONE_NAME}: {!VIEW}
									POST_NAME={SAVE_ID}
								{+END}
							</td>
						</tr>
						{+START,LOOP,PAGES}
							<tr>
								<td>
									&ndash; <kbd>{PAGE_NAME*}</kbd>
								</td>
								<td>
									{+START,INCLUDE,PERMISSIONS_CONTENT_ACCESS_TICK}
										LABEL={PAGE_NAME}: {!VIEW}
										POST_NAME={SAVE_ID}
									{+END}
								</td>
							</tr>
						{+END}
					{+END}
				</tbody>
			</table>
		</div>
	{+END}

	{+START,LOOP,MODULES}
		<h2>{CONTENT_TYPE_LABEL*}</h2>

		<div class="content-access-table-wrap">
			<table class="columned-table results-table responsive-table content-access-table" style="width: {$ADD*,20,{$MULT,5,{$ADD,1,{PRIVILEGES}}}}em">
				<colgroup>
					<col class="content-access-table-label" />
					<col class="content-access-table-setting" />
					{+START,LOOP,PRIVILEGES}
						<col class="content-access-table-setting" />
					{+END}
				</colgroup>

				<thead>
					<tr>
						<th>
							{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{!CATEGORY},UL_ESCAPED}{$KEEP}}
							<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{!CATEGORY}" alt="{!CATEGORY}" />
						</th>
						<th>
							{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE,{!VIEW},UL_ESCAPED}{$KEEP}}
							<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{!VIEW}" alt="{!VIEW}" />
						</th>
						{+START,LOOP,PRIVILEGES}
							<th>
								{$SET,url,{$FIND_SCRIPT_NOHTTP,gd_text}?trans_color={COLOR}&text={$ESCAPE*,{PRIVILEGE_LABEL},UL_ESCAPED}{$KEEP}}
								<img class="gd-text" data-gd-text="{}" src="{$GET*,url}" title="{PRIVILEGE_LABEL*}" alt="{PRIVILEGE_LABEL*}" />
							</th>
						{+END}
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>
							{!MODULE}
						</td>
						<td>
						</td>
						{+START,LOOP,PRIVILEGES}
							<td>
								{+START,INCLUDE,PERMISSIONS_CONTENT_ACCESS_LIST}
									LABEL={CONTENT_TYPE_LABEL}: {PRIVILEGE_LABEL}
									POST_NAME={SAVE_ID}__{PRIVILEGE_CODENAME}
								{+END}
							</td>
						{+END}
					</tr>
					{+START,LOOP,ITEMS}
						<tr>
							<td>
								{$SET,counter,0}{+START,WHILE,{$LT,{$GET,counter},{DEPTH}}}&ndash;{$INC,counter}{+END} {ITEM_LABEL*}
							</td>
							<td>
								{+START,INCLUDE,PERMISSIONS_CONTENT_ACCESS_TICK}
									LABEL={ITEM_LABEL}: {!VIEW}
									POST_NAME={SAVE_ID}
								{+END}
							</td>
							{+START,LOOP,ITEM_PRIVILEGES}
								<td>
									{+START,INCLUDE,PERMISSIONS_CONTENT_ACCESS_LIST}
										LABEL={ITEM_LABEL}: {PRIVILEGE_LABEL}
										POST_NAME={SAVE_ID}__{PRIVILEGE_CODENAME}
									{+END}
								</td>
							{+END}
						</tr>
					{+END}
				</tbody>
			</table>
		</div>
	{+END}

	<p>
		<button class="btn btn-primary btn-scr buttons--save" id="submit-button" accesskey="u" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} <span>{!SAVE}</span></button>
	</p>
</form>
