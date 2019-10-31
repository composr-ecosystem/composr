{$REQUIRE_JAVASCRIPT,iotds}

<div class="box box---iotd-box" data-tpl="iotdBox">
	<div class="box-inner">
		{+START,IF,{GIVE_CONTEXT}}
			{+START,SET,content_box_title}
				{+START,IF_NON_EMPTY,{I_TITLE}}{!CONTENT_IS_OF_TYPE,{!IOTD},{I_TITLE}}{+END}
				{+START,IF_EMPTY,{I_TITLE}}{!IOTD}{+END}
			{+END}
			{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
				<h3>{+START,IF_NON_EMPTY,{VIEW_URL}}<a class="subtle-link" href="{VIEW_URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{VIEW_URL}}</a>{+END}</h3>
			{+END}
		{+END}

		{+START,IF_NON_EMPTY,{THUMB_URL}}
			<div class="right float-separation">
				{+START,IF_NON_EMPTY,{VIEW_URL}}<a title="{I_TITLE*}" href="{VIEW_URL*}">{+END}<img alt="{!THUMBNAIL}" src="{THUMB_URL*}" />{+START,IF_NON_EMPTY,{VIEW_URL}}</a>{+END}
			</div>
		{+END}

		{+START,IF_PASSED,USERNAME}{+START,IF_NON_EMPTY,{USERNAME}}
			<div class="meta-details" role="note">
				<ul class="meta-details-list">
					<li>{!SUBMITTED_BY,<a href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>}</li>
				</ul>
			</div>
		{+END}{+END}

		{+START,IF_NON_EMPTY,{CAPTION}}
			<div class="associated-details">
				{$PARAGRAPH,{CAPTION}}
			</div>
		{+END}

		{+START,IF_PASSED,CHOOSE_URL}{+START,IF_PASSED,EDIT_URL}{+START,IF_PASSED,IS_CURRENT}
			<div style="margin-right: {$CONFIG_OPTION,thumb_width}px" class="buttons-group">
				<div class="buttons-group-inner">
					{+START,IF,{$NOT,{IS_CURRENT}}}
						<form title="{!CHOOSE} {!IOTD} #{ID*}" class="inline" action="{CHOOSE_URL*}" method="post">
							<input type="hidden" name="id" value="{ID*}" />
							<button class="btn btn-primary btn-scri buttons--choose" type="submit" title="{!CHOOSE} {!IOTD} #{ID*}">{+START,INCLUDE,ICON}NAME=buttons/choose{+END} {!CHOOSE}</button></form>
					{+END}
					<a class="btn btn-danger btn-scri" rel="edit" href="{EDIT_URL*}">{+START,INCLUDE,ICON}NAME=admin/edit{+END} <span>{!EDIT}: {!IOTD} #{ID*}</span></a>
					<form class="inline js-submit-confirm-iotd-deletion" title="{!DELETE} {!IOTD} #{ID*}" action="{DELETE_URL*}" method="post">{$INSERT_SPAMMER_BLACKHOLE}<input type="hidden" name="id" value="{ID*}" />
						<button class="btn btn-danger btn-scri" type="submit" value="{!DELETE}">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!DELETE} {!IOTD} #{ID*}</button>
					</form>
				</div>
			</div>
		{+END}{+END}{+END}
	</div>
</div>
