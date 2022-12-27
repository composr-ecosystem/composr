{$REQUIRE_JAVASCRIPT,galleries}

{$SET,RAND_FADER_IMAGE,{$RAND}}

{$SET,thumb_box_size,300}

{+START,IF,{$EQ,{BLOCK_ID},small_version}}
	<div class="box box---block-main-image-fader inline-block" data-view="BlockMainImageFader" data-view-params="{+START,PARAMS_JSON,RAND_FADER_IMAGE,MILL,TITLES,HTML,IMAGES,thumb_box_size}{_*}{+END}" data-keep-fix="{TITLES}{HTML}{IMAGES}{MILL}">
		<div class="box-inner">
			<h2>{!MEDIA}</h2>

			<div class="img-thumb-wrap">
				<a href="{GALLERY_URL*}"><img id="image-fader-{$GET%,RAND_FADER_IMAGE}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{$THUMBNAIL,{FIRST_URL},{$GET,thumb_box_size}}}" alt="" /></a>
			</div>
		</div>
	</div>
{+END}
{+START,IF,{$NEQ,{BLOCK_ID},small_version}}
	<div class="gallery-tease-pic-wrap" data-view="BlockMainImageFader" data-view-params="{+START,PARAMS_JSON,RAND_FADER_IMAGE,MILL,TITLES,HTML,IMAGES,thumb_box_size}{_*}{+END}" data-keep-fix="{TITLES}{HTML}{IMAGES}{MILL}">
		<div class="gallery-tease-pic">
		<div class="box box---gallery-tease-pic"><div class="box-inner">
			<div class="clearfix">
				<div class="gallery-tease-pic-pic">
					<div class="img-thumb-wrap">
						<a href="{GALLERY_URL*}"><img id="image-fader-{$GET%,RAND_FADER_IMAGE}" src="{$ENSURE_PROTOCOL_SUITABILITY*,{$THUMBNAIL,{FIRST_URL},{$GET,thumb_box_size}}}" alt="" /></a>
					</div>
				</div>

				<h2 id="image-fader-title-{$GET%,RAND_FADER_IMAGE}">{!MEDIA}</h2>

				<div class="gallery-tease-pic-teaser" id="image-fader-scrolling-text-{$GET%,RAND_FADER_IMAGE}">
					<span aria-busy="true"><img alt="" width="20" height="20" src="{$IMG*,loading}" /></span>
				</div>
			</div>
		</div></div>
	</div>
	</div>
{+END}
