{+START,INCLUDE,MEDIA_IMAGE_WEBSAFE,.tpl,templates,,1}{+END}

{+START,IF_NON_PASSED_OR_FALSE,WYSIWYG_EDITABLE}
	{+START,IF,{$AND,{FRAMED},{THUMB}}}
		{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
			<p class="associated-link associated-links-block-group"{+START,IF_NON_EMPTY,{WIDTH}}{+START,IF,{$NEQ,{WIDTH}x{HEIGHT},{$CONFIG_OPTION,thumb_width}x{$CONFIG_OPTION,thumb_width}}} style="width: {$ADD*,{WIDTH},10}px"{+END}{+END}>
				<a target="_blank" title="Choose thumbnail manually {!LINK_NEW_WINDOW}" href="{$BASE_URL*}/data_custom/upload-crop/upload_crop_v1.2.php?file={$REPLACE.*,{$BASE_URL}/,,{URL}}&amp;thumb={$REPLACE.*,{$BASE_URL}/,,{THUMB_URL}}&amp;thumb_width={$CONFIG_OPTION&*,thumb_width}&amp;thumb_height={$CONFIG_OPTION&*,thumb_width}{$KEEP*,0,1}">Choose thumbnail manually</a>
			</p>
		{+END}
	{+END}
{+END}
