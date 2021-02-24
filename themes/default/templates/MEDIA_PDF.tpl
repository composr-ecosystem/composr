{+START,SET,media}
	<iframe rel="noreferrer" width="{WIDTH*}" height="{HEIGHT*}" title="{!DOCUMENT}" class="gallery-pdf" src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}">{!DOCUMENT}</iframe>

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
