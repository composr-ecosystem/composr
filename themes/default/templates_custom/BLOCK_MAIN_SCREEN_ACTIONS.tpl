{$REQUIRE_JAVASCRIPT,facebook_support}

{+START,INCLUDE,BLOCK_MAIN_SCREEN_ACTIONS}
	{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}
		INSERT_AFTER: <nav class="screen-actions box-inner"> ~~> <div class="facebook-like"><div class="fb-like" data-send="false" data-layout="button_count" data-width="55" data-show-faces="false"></div></div>
		INSERT_BEFORE: <div class="facebook"> ~~> <!--
		INSERT_AFTER: <span>{!ADD_TO_FACEBOOK}</span></a></div> ~~> -->
	{+END}
{+END}
