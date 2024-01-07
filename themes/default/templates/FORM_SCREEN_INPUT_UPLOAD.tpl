{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{$,Must be outside of the template div unfortunately because the template has a max width to allow proper styling of the help button}
{+START,IF,{EDIT}}
	<p class="upload-field-msg inline-block">
		{+START,IF,{$AND,{IS_IMAGE},{$IS_NON_EMPTY,{EXISTING_URL}}}}
			<a rel="lightbox" href="{$ENSURE_PROTOCOL_SUITABILITY*,{EXISTING_URL}}"><img class="upload-field-image-preview" src="{$ENSURE_PROTOCOL_SUITABILITY*,{EXISTING_URL}}" title="" alt="{!EXISTING}" /></a>
		{+END}
		<input type="checkbox" id="i-{NAME*}-unlink" name="{NAME*}_unlink" value="1" />
		<label for="i-{NAME*}-unlink">
			{+START,IF,{$NOT,{$AND,{IS_IMAGE},{$IS_NON_EMPTY,{EXISTING_URL}}}}}
				{!UNLINK_EXISTING_UPLOAD}
			{+END}
			{+START,IF,{$AND,{IS_IMAGE},{$IS_NON_EMPTY,{EXISTING_URL}}}}
				{!UNLINK_EXISTING_UPLOAD_IMAGE,{$GET*,image_preview}}
			{+END}
		</label>
	</p>
{+END}

<div class="upload-field inline-block" data-view="FromScreenInputUpload" data-view-params="{+START,PARAMS_JSON,NAME,PLUPLOAD,FILTER,SYNDICATION_JSON}{_*}{+END}">
	<div class="vertical-alignment inline-block">
		<input tabindex="{TABINDEX*}" class="input-upload{REQUIRED*}" type="file" id="{NAME*}" name="{NAME*}" />
	</div>

	{+START,IF_PASSED,SYNDICATION_JSON}
		<div id="{NAME*}-syndication-options" class="syndication-options"></div>
	{+END}
</div>
