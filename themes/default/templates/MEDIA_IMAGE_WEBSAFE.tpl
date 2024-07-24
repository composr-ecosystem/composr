{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
	{+START,IF,{$OR,{$IN_STR,{$METADATA,image},/icons/},{$IS_EMPTY,{$METADATA,image}}}}
		{$METADATA,image,{THUMB_URL}}
	{+END}
{+END}

{+START,IF_PASSED_AND_TRUE,FRAMED}
	<figure class="attachment{+START,IF_PASSED,FLOAT} {FLOAT*} float-separation{+END}">
		<figcaption>{!IMAGE}</figcaption>
		<div>
			{+START,IF_NON_EMPTY,{DESCRIPTION}}
				{$,Extra div needed to stop WYSIWYG editor making a mess}
				<div {+START,IF,{$NEQ,{WIDTH}x{HEIGHT},x,{$CONFIG_OPTION,thumb_width}x{$CONFIG_OPTION,thumb_width}}} style="width: {$MAX*,{WIDTH},80}px"{+END}>
					{$PARAGRAPH,{DESCRIPTION}}
				</div>
			{+END}

			<div class="attachment-details">
				{+START,IF,{THUMB}}<a
					{+START,IF,{$NOT,{$INLINE_STATS}}} data-click-stats-event-track="{ category: '{!IMAGE;^*}', action: '{FILENAME;^*}', nativeTracking: false }"{+END}
					target="_blank"
					title="{!LINK_NEW_WINDOW}"
					{+START,IF_PASSED,CLICK_URL}href="{CLICK_URL*}"{+END}
					{+START,IF_NON_PASSED,CLICK_URL}
						rel="lightbox"
						href="{URL*}"
					{+END}
				>{+END}<img
					{+START,IF,{$NEQ,{WIDTH}x{HEIGHT},{$CONFIG_OPTION,thumb_width}x{$CONFIG_OPTION,thumb_width}}}
						{+START,IF_NON_EMPTY,{WIDTH}}width="{WIDTH*}"{+END}
						{+START,IF_NON_EMPTY,{HEIGHT}}height="{HEIGHT*}"{+END}
					{+END}
					{+START,IF_NON_EMPTY,{DESCRIPTION}}alt="{DESCRIPTION*}"{+END}
					{+START,IF_EMPTY,{DESCRIPTION}}alt=""{+END}
					src="{$ENSURE_PROTOCOL_SUITABILITY*,{THUMB_URL}}"
					loading="lazy"
				/>{+START,IF,{THUMB}}</a>{+END}

				{$,Uncomment for a download link \{+START,INCLUDE,MEDIA__DOWNLOAD_LINK\}\{+END\}}

				{+START,IF,{THUMB}}{+START,IF_NON_PASSED,CLICK_URL}<p class="associated-details">({!comcode:CLICK_TO_ENLARGE})</p>{+END}{+END}
			</div>
		</div>
	</figure>
{+END}
{+START,IF_NON_PASSED_OR_FALSE,FRAMED}
	{+START,IF_PASSED,CLICK_URL}{+START,IF,{$NOT,{THUMB}}}<a href="{CLICK_URL*}">{+END}{+END}{+START,IF,{THUMB}}<a
		target="_blank"
		title="{+START,IF_NON_EMPTY,{DESCRIPTION}}{DESCRIPTION*} {+END}{!LINK_NEW_WINDOW}"
		{+START,IF_PASSED,CLICK_URL}href="{CLICK_URL*}"{+END}
		{+START,IF_NON_PASSED,CLICK_URL}
			rel="lightbox"
			href="{URL*}"
		{+END}
	>{+END}<img
		{+START,IF,{$NEQ,{WIDTH}x{HEIGHT},{$CONFIG_OPTION,thumb_width}x{$CONFIG_OPTION,thumb_width}}}
			{+START,IF_NON_EMPTY,{WIDTH}}width="{WIDTH*}"{+END}
			{+START,IF_NON_EMPTY,{HEIGHT}}height="{HEIGHT*}"{+END}
		{+END}

		{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
			{+START,IF,{THUMB}}
				{+START,IF_PASSED,NUM_DOWNLOADS}
					alt="{!IMAGE_ATTACHMENT,{$INTEGER_FORMAT*,{NUM_DOWNLOADS},0},{CLEAN_FILESIZE*}}"
				{+END}
				{+START,IF_NON_PASSED,NUM_DOWNLOADS}
					alt="{DESCRIPTION*}"
				{+END}
			{+END}

			{+START,IF,{$NOT,{THUMB}}}
				alt="{DESCRIPTION*}"
			{+END}
		{+END}

		{+START,IF_PASSED_AND_TRUE,WYSIWYG_EDITABLE}
			alt="{DESCRIPTION*}"
			{+START,IF_PASSED,FLOAT}style="float: {FLOAT*}"{+END}
			class="attachment-img float-separation"
		{+END}
		{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
			class="attachment-img{+START,IF_PASSED,FLOAT} {FLOAT*} float-separation{+END}"
		{+END}

		src="{$ENSURE_PROTOCOL_SUITABILITY*,{THUMB_URL}}"
		loading="lazy"
	/>{+START,IF,{THUMB}}</a>{+END}{+START,IF_PASSED,CLICK_URL}{+START,IF,{$NOT,{THUMB}}}</a>{+END}{+END}
{+END}
