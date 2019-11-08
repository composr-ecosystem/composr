<div class="global-middle" data-view="AttachmentsBrowser" data-view-params="{+START,PARAMS_JSON,FIELD_NAME,ID,DESCRIPTION}{_*}{+END}">
	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
		{$INSERT_SPAMMER_BLACKHOLE}

		<label for="member_id">{!ATTACHMENTS_OF}:
		<select id="member_id" name="member_id" class="form-control">
			{LIST}
		</select></label>

		<button data-disable-on-click="1" class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</form>

	<hr class="spaced-rule" />

	{+START,LOOP,ATTACHMENTS}
		{TPL}

		<div class="buttons-group">
			<div class="buttons-group-inner">
				<a class="btn btn-primary btn-scri buttons--choose js-click-do-attachment-and-close" href="#!">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} <span>{!CHOOSE}</span></a>

				{+START,IF,{MAY_DELETE}}
					<form title="{!DELETE}" class="inline" method="post" action="{DELETE_URL*}">
						{$INSERT_SPAMMER_BLACKHOLE}

						<input type="hidden" name="delete_{ID*}" value="1" />
						<button data-cms-confirm-click="{!ARE_YOU_SURE_DELETE}" type="submit" class="btn btn-danger btn-scri">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!DELETE}</button>
					</form>
				{+END}
			</div>
		</div>

		<hr class="spaced-rule" />
	{+END}
	{+START,IF_EMPTY,{ATTACHMENTS}}
		<p class="nothing-here">
			{!NO_ENTRIES}
		</p>
	{+END}
</div>
