{$REQUIRE_CSS,stepper}

<div class="stepper-wrapper">
	{+START,LOOP,RANKS}
		{+START,IF,{$EQ,{SIMPLE_STEPPER},0}}
			<div class="stepper-item{+START,IF,{$GT,{RANK_STATUS},1}} completed{+END}{+START,IF,{$EQ,{RANK_STATUS},1}} active{+END}">
				<div class="step-counter" data-cms-tooltip="{ contents: '{!RANK_STEPPER_TOOLTIP;^=,{RANK_NAME},{RANK_THRESHOLD}}'}">{RANK_THRESHOLD*}</div>
				<div class="step-name">{RANK_NAME*}</div>
			</div>
		{+END}
		{+START,IF,{$EQ,{SIMPLE_STEPPER},1}}
			<div class="stepper-item simple{+START,IF,{$GT,{RANK_STATUS},1}} completed{+END}{+START,IF,{$EQ,{RANK_STATUS},1}} active{+END}">
				<div class="step-counter" data-cms-tooltip="{ contents: '{!RANK_STEPPER_TOOLTIP;^=,{RANK_NAME},{RANK_THRESHOLD}}'}"> </div>
			</div>
		{+END}
	{+END}
</div>

{+START,IF,{$GT,{_NUM_POINTS_ADVANCE},0}}
	<p>{!RANK_SUMMARY,{CURRENT_RANK*},{NUM_POINTS_ADVANCE*}}</p>
{+END}
{+START,IF,{$NOT,{$GT,{_NUM_POINTS_ADVANCE},0}}}
	<p>{!RANK_SUMMARY_NO_PROGRESSION,{CURRENT_RANK*}}</p>
{+END}

<hr>

<p>{!RANK_BASED_PRIVILEGES_TEXT}</p>

{+START,IF_NON_EMPTY,{UNLOCKED_THEMES}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/adminzone/style/themes/themes}"></img>{!RANK_UNLOCKED_THEMES,{UNLOCKED_THEMES*}}</p>{+END}
{+START,IF,{$EQ,{HAS_DAILY_UPLOAD_QUOTA},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/buttons/upload}"></img>{!RANK_DAILY_UPLOAD_QUOTA_UNLIMITED}</p>{+END}
{+START,IF,{$EQ,{HAS_DAILY_UPLOAD_QUOTA},1}}{+START,IF,{$GT,{_DAILY_UPLOAD_QUOTA},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/buttons/upload}"></img>{!RANK_DAILY_UPLOAD_QUOTA,{DAILY_UPLOAD_QUOTA*}}</p>{+END}{+END}
{+START,IF,{$GT,{_MAX_ATTACHMENTS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/buttons/save}">{!RANK_MAX_ATTACHMENTS,{MAX_ATTACHMENTS*}}</p>{+END}
{+START,IF,{$GT,{_POST_LENGTH},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/buttons/new_post_full}">{!RANK_POST_LENGTH,{POST_LENGTH*}}</p>{+END}
{+START,IF,{$GT,{_SIGNATURE_LENGTH},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/tabs/member_account/edit/signature}">{!RANK_SIGNATURE_LENGTH,{SIGNATURE_LENGTH*}}</p>{+END}
{+START,IF,{$EQ,{CAN_UPLOAD_AVATARS},1}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/tabs/member_account/edit/avatar}">{!RANK_MAXIMUM_AVATAR_DIMENSIONS,{MAXIMUM_AVATAR_DIMENSIONS*}}</p>{+END}
{+START,IF,{$EQ,{INFINITE_PERSONAL_GALLERY_ENTRIES},0}}{+START,IF,{$GT,{_PERSONAL_GALLERY_ENTRIES_IMAGES},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/content_types/image}">{!RANK_PERSONAL_GALLERY_ENTRIES_IMAGES,{PERSONAL_GALLERY_ENTRIES_IMAGES*}}</p>{+END}{+END}
{+START,IF,{$EQ,{INFINITE_PERSONAL_GALLERY_ENTRIES},0}}{+START,IF,{$GT,{_PERSONAL_GALLERY_ENTRIES_VIDEOS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/content_types/video}">{!RANK_PERSONAL_GALLERY_ENTRIES_VIDEOS,{PERSONAL_GALLERY_ENTRIES_VIDEOS*}}</p>{+END}{+END}
{+START,IF,{$EQ,{INFINITE_PERSONAL_GALLERY_ENTRIES},1}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/content_types/multimedia}">{!RANK_PERSONAL_GALLERY_ENTRIES_UNLIMITED}</p>{+END}
{+START,IF,{$GT,{_GIFT_POINTS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/social/points}">{!RANK_GIFT_POINTS,{GIFT_POINTS*}}</p>{+END}
{+START,IF,{$GT,{_GIFT_POINTS_PER_DAY},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/social/points}">{!RANK_GIFT_POINTS_PER_DAY,{GIFT_POINTS_PER_DAY*}}</p>{+END}
{+START,LOOP,UNLOCKED_PRIVILEGES}
	<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/adminzone/security/permissions/privileges}">{!RANK_UNLOCKED_PRIVILEGE,{PRIVILEGE*},{SCOPE*}}</p>
{+END}

{+START,IF,{$GT,{_FLOOD_CONTROL_SUBMIT},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/adminzone/security}">{!RANK_FLOOD_CONTROL_SUBMIT,{FLOOD_CONTROL_SUBMIT*}}</p>{+END}
{+START,IF,{$GT,{_FLOOD_CONTROL_ACCESS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/menu/adminzone/security}">{!RANK_FLOOD_CONTROL_ACCESS,{FLOOD_CONTROL_ACCESS*}}</p>{+END}

{+START,IF_NON_EMPTY,{LOCKED_THEMES}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_THEMES,{LOCKED_THEMES*}}</p>{+END}
{+START,IF,{$EQ,{HAS_DAILY_UPLOAD_QUOTA},1}}{+START,IF,{$EQ,{_DAILY_UPLOAD_QUOTA},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_UPLOAD_ATTACHMENTS}</p>{+END}{+END}
{+START,IF,{$EQ,{_MAX_ATTACHMENTS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_ADD_ATTACHMENTS}</p>{+END}
{+START,IF,{$EQ,{_POST_LENGTH},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_POSTS}</p>{+END}
{+START,IF,{$EQ,{_SIGNATURE_LENGTH},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_SIGNATURE}</p>{+END}
{+START,IF,{$EQ,{INFINITE_PERSONAL_GALLERY_ENTRIES},0}}{+START,IF,{$EQ,{_PERSONAL_GALLERY_ENTRIES_IMAGES},0}}{+START,IF,{$EQ,{_PERSONAL_GALLERY_ENTRIES_VIDEOS},0}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_PERSONAL_GALLERY}</p>{+END}{+END}{+END}
{+START,IF,{$AND,{$EQ,{_GIFT_POINTS},0},{$EQ,{_GIFT_POINTS_PER_DAY},0}}}<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_GIFT_POINTS}</p>{+END}
{+START,LOOP,LOCKED_PRIVILEGES}
	<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/cns_topic_modifiers/closed}">{!RANK_LOCKED_PRIVILEGE,{PRIVILEGE*},{SCOPE*}}</p>
{+END}
