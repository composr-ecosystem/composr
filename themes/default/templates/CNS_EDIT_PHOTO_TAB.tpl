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
			<form title="{$WCASE,{!DELETE_PHOTO}}" action="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}#tab--edit--photo" method="post" class="inline">
				{$INSERT_SPAMMER_BLACKHOLE}

				<p>
					<input type="hidden" name="delete_photo" value="1" />
					{!YOU_CAN_DELETE_PHOTO,<button class="button-hyperlink" type="submit">{!DELETE_PHOTO}</button>}
				</p>
			</form>
		{+END}
	</div>
</div>
