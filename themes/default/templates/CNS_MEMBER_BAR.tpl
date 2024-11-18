<section id="tray-{!MEMBER|}" data-toggleable-tray="{ save: true }" class="box box--cns-member-bar cns-information-bar-outer">
	<div class="box-inner">
		<h2 class="toggleable-tray-title">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray inline-desktop" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
				NAME=trays/contract
				ICON_SIZE=24
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!MEMBER_INFORMATION,{$USERNAME*,{$MEMBER},1}}{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}} / {!SEARCH}{+END}{+END}</a>
		</h2>

		<div class="toggleable-tray js-tray-content">
			<div class="cns-information-bar clearfix">
				{+START,IF_NON_EMPTY,{AVATAR_URL}}
					<div style="min-height: {$MAX,100,{MAX_AVATAR_HEIGHT|}}px" class="cns-member-column cns-member-column-a">
						<img alt="{!AVATAR}" title="{!AVATAR}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" />
					</div>
				{+END}

				<div style="min-height: {$MAX,100,{MAX_AVATAR_HEIGHT|}}px" class="cns-member-column cns-member-column-b">
					<p class="cns-member-column-title">{!WELCOME_BACK,<a href="{PROFILE_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>}</p>
					{+START,IF,{$NOT,{$IS_HTTPAUTH_LOGIN}}}
						<div class="inline-desktop">
							<form class="inline associated-link" title="{!LOGOUT}" method="post" action="{LOGOUT_URL*}"><button class="button-hyperlink" type="submit">{!LOGOUT}</button></form>
						</div>
					{+END}

					<dl class="meta-details-list">
						{+START,IF,{$ADDON_INSTALLED,points}}
							<dt class="field-name"><abbr title="{!RANK_POINTS,{$INTEGER_FORMAT*,{$POINTS_BALANCE},0}}">{!POINTS}</abbr>:</dt> <dd><a {+START,IF_PASSED,NUM_POINTS_ADVANCE} title="{!GROUP_ADVANCE,{NUM_POINTS_ADVANCE*}}"{+END} href="{$PAGE_LINK*,site:points:member:{$MEMBER}}">{NUM_POINTS*}</a></dd>
						{+END}
						<dt class="field-name">{!COUNT_POSTS}:</dt> <dd>{NUM_POSTS*}</dd>
						<dt class="field-name">{$?,{$MOBILE},{!USERGROUP},{!PRIMARY_GROUP}}:</dt> <dd>{PRIMARY_GROUP*}</dd>
					</dl>
				</div>

				<div style="min-height: {$MAX,100,{MAX_AVATAR_HEIGHT|}}px" class="cns-member-column cns-member-column-c">
					{+START,IF,{$ADDON_INSTALLED,search}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,search}}
						<div class="box nested"><div class="box-inner">{+START,INCLUDE,MEMBER_BAR_SEARCH}{+END}</div></div>
					{+END}{+END}

					<div class="cns-member-column-last-visit">{!LAST_VISIT,{LAST_VISIT_DATE*}}
						<ul class="meta-details-list">
							<li>{!NEW_TOPICS,{NEW_TOPICS*}}</li>
							<li>{!NEW_POSTS,{NEW_POSTS*}}</li>
						</ul>
					</div>
				</div>

				<nav style="min-height: {$MAX,100,{MAX_AVATAR_HEIGHT|}}px" class="cns-member-column cns-member-column-d">
					{$,<p class="cns-member-column-title">{!VIEW}:</p>}
					<ul class="actions-list">
						<!--<li><a href="{PRIVATE_TOPIC_URL*}">{!PRIVATE_TOPICS}{+START,IF_NON_EMPTY,{PT_EXTRA}} <span class="cns-member-column-pts">{PT_EXTRA}</span>{+END}</a></li>-->
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{NEW_POSTS_URL*}">{!POSTS_SINCE}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{UNREAD_TOPICS_URL*}">{!TOPICS_UNREAD}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{RECENTLY_READ_URL*}">{!RECENTLY_READ}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{INLINE_PERSONAL_POSTS_URL*}">{!INLINE_PERSONAL_POSTS}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{UNANSWERED_TOPICS_URL*}">{!UNANSWERED_TOPICS}</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a {+START,IF,{$DESKTOP}} data-open-as-overlay="{}"{+END} href="{INVOLVED_TOPICS_URL*}">{!INVOLVED_TOPICS}</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
</section>
