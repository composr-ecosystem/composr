{$,
<section class="box box---block-main-facebook-page"><div class="box-inner">
	<h3>{!facebook:FACEBOOK_PAGE}</h3>
}

	<div class="fb-page" data-href="https://www.facebook.com/{$CONFIG_OPTION*,facebook_uid}" data-width="{WIDTH*}" data-height="{HEIGHT*}" data-hide-cover="{$?,{SHOW_COVER_PHOTO},false,true}" data-show-facepile="{$?,{SHOW_FANS},true,false}" data-show-posts="{$?,{SHOW_POSTS},true,false}">
		<div class="fb-xfbml-parse-ignore">
			<blockquote cite="https://www.facebook.com/{$CONFIG_OPTION*,facebook_uid}">
				<a rel="noopener" href="https://www.facebook.com/{$CONFIG_OPTION*,facebook_uid}">{PAGE_NAME*}</a>
			</blockquote>
		</div>
	</div>

{$,
</div></section>
}
