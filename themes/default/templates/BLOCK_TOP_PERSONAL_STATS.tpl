<div class="top-button-wrapper" data-tpl="blockTopPersonalStats">
	<a title="{$STRIP_TAGS,{!LOGGED_IN_AS,{USERNAME*}}} ({$IP_ADDRESS*}, #{MEMBER_ID*})" id="top-personal-stats-button" class="top-button js-click-toggle-button-popup" data-click-pd="1" href="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}">{+START,INCLUDE,ICON}
		NAME=content_types/member
		ICON_SIZE=24
	{+END}</a>
	<div class="top-button-popup" id="top-personal-stats-rel" style="display: none">
		<div class="box box-arrow box--block-top-personal-stats"><div class="box-inner"><div>
			{+START,IF_NON_EMPTY,{AVATAR_URL}}
				<div class="personal-stats-avatar"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" title="{!AVATAR}" alt="{!AVATAR}" /></div>
			{+END}

			<h3>{$DISPLAYED_USERNAME*,{USERNAME}}</h3>

			{+START,IF_NON_EMPTY,{DETAILS}}
				<ul class="compact-list">
					{DETAILS}
				</ul>
			{+END}

			{+START,IF_NON_EMPTY,{LINKS_ECOMMERCE}}
				<ul class="associated-links-block-group">
					{LINKS_ECOMMERCE}
				</ul>
			{+END}

			{+START,IF_NON_EMPTY,{LINKS}}
				<ul class="associated-links-block-group horizontal-links">
					{LINKS}
				</ul>
			{+END}
		</div></div></div>
	</div>
</div>
