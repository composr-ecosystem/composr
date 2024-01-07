{$,Must be outside of the template div unfortunately because the template has a max width to allow proper styling of the help button}
{+START,IF_NON_EMPTY,{EDIT}}
	{$, If you want to let people remove all in one tick
	<p class="upload-field-msg inline-block">
		<input type="checkbox" id="i-{NAME_STUB*}-unlink" name="{NAME_STUB*}_unlink" value="1" />
		<label for="i-{NAME_STUB*}-unlink">
			{!UNLINK_EXISTING_UPLOADS}
		</label>
	</p>
	}

	{+START,LOOP,EDIT}
		<p class="upload-field-msg inline-block">
			{+START,IF,{$AND,{IS_IMAGE},{$IS_NON_EMPTY,{URL}}}}
				<a rel="lightbox" href="{$ENSURE_PROTOCOL_SUITABILITY*,{IMAGE_URL}}"><img class="upload-field-image-preview" src="{$ENSURE_PROTOCOL_SUITABILITY*,{IMAGE_URL}}" title="" alt="{$ENSURE_PROTOCOL_SUITABILITY*,{IMAGE_URL}}" /></a>
			{+END}
			<input type="checkbox" id="i-{NAME_STUB*}-{$ADD*,{INDEX},1}-unlink" name="{NAME_STUB*}_{$ADD*,{INDEX},1}_unlink" value="1" />
			<label for="i-{NAME_STUB*}-{$ADD*,{INDEX},1}-unlink">
				{!UNLINK_EXISTING_UPLOAD_SPECIFIC,<kbd>{$URLDECODE*,{$PREG_REPLACE,::.*/,,{URL}}}</kbd>}
			</label>
		</p>
	{+END}
{+END}

<div class="upload-field inline-block multi-field" data-tpl="formScreenInputUploadMulti" data-tpl-params="{+START,PARAMS_JSON,SYNDICATION_JSON,PLUPLOAD,NAME_STUB,I,FILTER}{_*}{+END}">
	<div class="accessibility-hidden"><label for="{NAME_STUB*}_{I*}">{!UPLOAD}</label></div>
	<div class="vertical-alignment inline-block">
		<input tabindex="{TABINDEX*}" class="input-upload{REQUIRED*} js-input-change-ensure-next-field-upload" type="file" id="{NAME_STUB*}_{I*}" name="{NAME_STUB*}_{I*}" />

		<input type="hidden" name="label_for_{NAME_STUB*}_{I*}" value="{!UPLOAD}" />

		<button class="btn btn-primary btn-sm buttons--clear js-click-clear-name-stub-input" type="button" id="clear-button-{NAME_STUB*}_{I*}">{+START,INCLUDE,ICON}NAME=buttons/clear{+END} <span>{!CLEAR}</span></button>

		<!--Additional uploaders will auto-append here-->
	</div>

	{+START,IF_PASSED,SYNDICATION_JSON}
		<div id="{NAME_STUB*}-syndication-options" class="syndication-options"></div>
	{+END}
</div>
