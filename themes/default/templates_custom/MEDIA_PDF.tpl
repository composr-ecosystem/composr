{+START,INCLUDE,MEDIA_PDF}
	STR_REPLACE: src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" ~~> style="clear: both" src="{$BASE_URL*}/data_custom/pdf_viewer/web/viewer.html?file={URL&*}#zoom=90"
{+END}
