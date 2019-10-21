<div class="gallery-slideshow-screen" id="slideshow-{SLIDESHOW_ID*}" data-view="GallerySlideshowScreen" data-view-params="{+START,PARAMS_JSON,TOTAL_ITEMS}{_*}{+END}" itemscope="itemscope" itemtype="http://schema.org/{+START,IF_PASSED,VIDEO}Video{+END}{+START,IF_NON_PASSED,VIDEO}Image{+END}Object">
	<div class="slideshow-content">
		<div class="slideshow-status">{!VIEWING_SLIDE,{$ROUND,{$ADD,1,{CURRENT_INDEX}}},{TOTAL_ITEMS}}</div>

		<div class="slideshow-main">
			<div class="slideshow-media-box cms-slider cms-slider-slide">
				<div class="slideshow-media-box-item cms-slider-item is-{CURRENT_TYPE*} active" data-vw-index="{CURRENT_INDEX*}">
					<div class="slideshow-media-wrapper">
					{+START,IF_NON_PASSED,CURRENT_VIDEO}
						<img class="slideshow-img" src="{$ENSURE_PROTOCOL_SUITABILITY*,{CURRENT_URL}}" alt="" itemprop="contentURL" />
					{+END}
					{+START,IF_PASSED,CURRENT_VIDEO}
						{CURRENT_VIDEO}
					{+END}
					</div>
				</div>
			</div>

			<button type="button" class="btn-slider-control btn-slider-control-prev">
				<i class="chevron chevron-left"></i>
				<span class="sr-only">{!PREVIOUS*}</span>
			</button>
			<button type="button" class="btn-slider-control btn-slider-control-next">
				<i class="chevron chevron-right"></i>
				<span class="sr-only">{!NEXT*}</span>
			</button>
		</div>

		<div class="slideshow-progress-bar">
			<div class="slideshow-progress-bar-fill"></div>
		</div>

		{$REQUIRE_CSS,widget_glide}
		{$REQUIRE_JAVASCRIPT,glide}

		<div class="slideshow-carousel glide" data-focus-class="focus-within">
			<div class="glide__track" data-glide-el="track">
				<div class="glide__slides">
					{CAROUSEL_ENTRIES}
				</div>
			</div>
		</div>
	</div>

	<div class="slideshow-menu">
		<button type="button" class="btn btn-secondary btn-exit-slideshow" style="display: none;">{+START,INCLUDE,ICON}NAME=buttons/cancel{+END}</button>

		<div class="slideshow-menu-middle">
			<button type="button" class="btn btn-primary btn-toggle-play" data-cms-tooltip="{!PLAY_OR_PAUSE*}">{+START,INCLUDE,ICON}NAME=content_types/multimedia{+END}</button>
			<button type="button" class="btn btn-secondary btn-toggle-details" data-cms-tooltip="{!SHOW_OR_HIDE_CAPTIONS*}">{+START,INCLUDE,ICON}NAME=menu/pages/about_us{+END}</button>
			<button type="button" class="btn btn-secondary btn-toggle-fullscreen" data-cms-tooltip="{!TOGGLE_FULLSCREEN*}">{+START,INCLUDE,ICON}NAME=buttons/full_size{+END}</button>
			<button type="button" class="desktop-only btn btn-secondary btn-toggle-tab btn-toggle-comments" data-cms-tooltip="{!SHOW_OR_HIDE_COMMENTS*}" data-vw-tab="comments">{+START,INCLUDE,ICON}NAME=feedback/comment{+END}</button>
			<button type="button" class="desktop-only btn btn-secondary btn-toggle-tab btn-toggle-settings" data-cms-tooltip="{!SHOW_OR_HIDE_SETTINGS*}" data-vw-tab="settings">{+START,INCLUDE,ICON}NAME=buttons/settings{+END}</button>
		</div>
	</div>

	<div class="slideshow-tab slideshow-tab-comments compact-comments-space" data-vw-tab="comments" style="display: none">
		<div class="slideshow-tab-inner">
			{+START,IF_EMPTY,{$TRIM,{CURRENT_COMMENT_DETAILS}}}
				{!COMMENTS_DISABLED}
			{+END}

			{+START,IF_NON_EMPTY,{$TRIM,{CURRENT_COMMENT_DETAILS}}}
				{CURRENT_COMMENT_DETAILS}
			{+END}
		</div>
	</div>

	<div class="slideshow-tab slideshow-tab-settings" data-vw-tab="settings" style="display: none">
		<form class="slideshow-tab-inner" action="#" title="{!SETTINGS*}">
			<h3>{!SETTINGS}</h3>

			<div class="setting">
				<label for="input-slide-duration">
					<span class="setting-label">{!SPEED_IN_SECS}</span>
					<input type="number" id="input-slide-duration" name="slide_duration" class="form-control input-slide-duration" value="5" min="1" />
				</label>
			</div>

			<div class="setting">
				<label for="select-slide-transition-effect">
					<span class="setting-label">{!SLIDE_TRANSITION_EFFECT}</span>
					<select name="slide_transition_effect" id="select-slide-transition-effect" class="form-control select-slide-transition-effect">
						<option value="slide" selected="selected">{!SLIDE}</option>
						<option value="fade">{!FADE}</option>
					</select>
				</label>
			</div>

			<div class="setting">
				<label for="checkbox-stretch-small-media">
					<span class="setting-label">{!STRETCH_SMALL_MEDIA}</span>
					<input type="checkbox" name="stretch_small_media" id="checkbox-stretch-small-media" class="big-checkbox checkbox-stretch-small-media" checked="checked" />
				</label>
			</div>

			<div class="setting">
				<label for="select-background-color">
					<span class="setting-label">{!BACKGROUND_COLOR}</span>
					<select name="background_color" id="select-background-color" class="form-control select-background-color">
						<option value="dark" selected="selected">{!DARK}</option>
						<option value="light">{!LIGHT}</option>
					</select>
				</label>
			</div>
		</form>
	</div>
</div>
