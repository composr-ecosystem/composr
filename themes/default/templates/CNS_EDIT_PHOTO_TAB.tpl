<div class="clearfix">
	<div class="cns-avatar-page-old-avatar">
		{+START,IF_NON_EMPTY,{PHOTO}}
			<img class="cns-topic-post-avatar" alt="{!PHOTO}" src="{$THUMBNAIL*,{PHOTO}}" />
		{+END}
		{+START,IF_EMPTY,{PHOTO}}
			{!NONE_EM}
		{+END}
	</div>

	<div class="cns-avatar-page-text">
		<p>{!PHOTO_CHANGE,{$DISPLAYED_USERNAME*,{USERNAME}}}</p>

		{TEXT}

		{+START,IF_NON_EMPTY,{PHOTO}}
			<p>
				<input type="hidden" name="delete_photo" value="0" />
				{!YOU_CAN_DELETE_PHOTO,<button class="js-delete-photo button-hyperlink" type="button">{!DELETE_PHOTO}</button>}
			</p>
		{+END}
	</div>
</div>
