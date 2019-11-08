{+START,SET,media}
	<iframe width="{WIDTH*}" height="{HEIGHT*}" title="{!DOCUMENT}" style="clear: both" class="gallery-pdf" src="{$BASE_URL*}/data_custom/pdf_viewer/web/viewer.html?file={URL&*}#zoom=90">{!DOCUMENT}</iframe>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<figcaption class="associated-details">
			{$PARAGRAPH,{DESCRIPTION}}
		</figcaption>
	{+END}

	{$,Uncomment for a download link \{+START,INCLUDE,MEDIA__DOWNLOAD_LINK\}\{+END\}}
{+END}
{+START,IF_PASSED_AND_TRUE,FRAMED}
	<figure>
		{$GET,media}
	</figure>
{+END}
{+START,IF_NON_PASSED_OR_FALSE,FRAMED}
	{$GET,media}
{+END}
