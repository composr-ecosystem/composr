<nav>
	<ul class="actions-list">
		<li class="actions-list-strong">
			{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}
			<a {+START,IF,{$NOT,{$INLINE_STATS}}} data-click-stats-event-track="{ category: '{!_ATTACHMENT;^*}', action: '{FILENAME;^*}', nativeTracking: false }"{+END} class="user-link" rel="enclosure" target="_blank" title="{!_DOWNLOAD,{FILENAME*}} {!LINK_NEW_WINDOW}" href="{URL*}">{!_DOWNLOAD,{FILENAME*}}</a>

			{+START,IF_NON_EMPTY,{CLEAN_FILESIZE}{$GET,num_downloads}}
				{+START,SET,num_downloads}{+START,IF_PASSED,NUM_DOWNLOADS}{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}{+START,IF,{$INLINE_STATS}}{!DOWNLOADS_SO_FAR,{$NUMBER_FORMAT*,{NUM_DOWNLOADS}}}{+END}{+END}{+END}{+END}
				({+START,IF_NON_EMPTY,{CLEAN_FILESIZE}}{CLEAN_FILESIZE*}{+START,IF_NON_EMPTY,{$GET,num_downloads}}, {+END}{+END}{$GET,num_downloads})
			{+END}
		</li>
	</ul>
</nav>
