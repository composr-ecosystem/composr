<div class="cns-forum-grouping" data-toggleable-tray="{}">
	<h3 class="cns-forum-grouping-heading">
		<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{GROUPING_TITLE*}</a>
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_cns_forum_groupings}}<span class="associated-details">(<a title="{!EDIT} {GROUPING_TITLE*}" href="{$PAGE_LINK*,_SEARCH:admin_cns_forum_groupings:_edit:{GROUPING_ID}}">{!EDIT}</a>)</span>{+END}

		{+START,IF_NON_EMPTY,{GROUPING_DESCRIPTION}}
			&ndash; <span class="associated-details">{GROUPING_DESCRIPTION*}</span>
		{+END}

		<span class="cns-forum-grouping-toggleable-tray-button">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!TOGGLE_GROUPING_VISIBILITY}">{+START,INCLUDE,ICON}
				NAME=trays/{EXPAND_TYPE}
				ICON_SIZE=24
			{+END}</a>
		</span>
	</h3>

	<div class="toggleable-tray js-tray-content" id="c-{GROUPING_ID*}"{+START,IF,{$NEQ,{DISPLAY},block}} style="display: {DISPLAY*}"{+END}>
		<table class="columned-table wide-table cns-forum-grouping" itemprop="significantLinks">
			{+START,IF,{$DESKTOP}}
				<colgroup>
					<col class="cns-forum-grouping-column1 column-desktop" />
					<col class="cns-forum-grouping-column2" />
					<col class="cns-forum-grouping-column3 column-desktop" />
					<col class="cns-forum-grouping-column4 column-desktop" />
					<col class="cns-forum-grouping-column5" />
				</colgroup>
			{+END}

			<thead>
				<tr>
					{+START,IF,{$DESKTOP}}
						<th class="ocf-forum-box-left cell-desktop"><span class="accessibility-hidden">{!FORUM}: {!STATUS}</span></th>
					{+END}
					<th {+START,IF,{$MOBILE}} class="cns-forum-box-left"{+END}>
						{!FORUM_NAME}
					</th>
					{+START,IF,{$DESKTOP}}
						<th class="cns-forum-grouping-centered-header cell-desktop">
							{!COUNT_TOPICS}
						</th>
						<th class="cns-forum-grouping-centered-header cell-desktop">
							{!COUNT_POSTS}
						</th>
					{+END}
					<th class="cns-forum-box-right">
						{!LAST_POST}
					</th>
				</tr>
			</thead>

			<tbody>
				{FORUMS}
			</tbody>
		</table>
		<div class="cns-table-footer"><div><div>
			{+START,IF,{$DESKTOP}}
				<div class="cns-column1 cns-forum-box-bleft"></div>
			{+END}
			<div class="cns-column1{+START,IF,{$MOBILE}} cns-forum-box-bleft{+END}"></div>
			{+START,IF,{$DESKTOP}}
				<div class="cns-column1 block-desktop"></div>
				<div class="cns-column1 block-desktop"></div>
			{+END}
			<div class="cns-column1 cns-forum-box-bright"></div>
		</div></div></div>
	</div>
</div>
