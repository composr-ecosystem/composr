{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<div data-tpl="previewScript">
	{+START,IF_NON_EMPTY,{SPELLING}}
		<div class="box box---preview-script"><div class="box-inner">
			<h2>{!SPELLCHECK}</h2>

			<div>
				{SPELLING}
			</div>
		</div></div>
	{+END}

	{+START,IF_NON_EMPTY,{KEYWORD_DENSITY}}
		<div class="box box---preview-script"><div class="box-inner">
			<h2>{!KEYWORDCHECK}</h2>

			<div>
				{KEYWORD_DENSITY}
			</div>
		</div></div>
	{+END}

	{+START,IF_NON_EMPTY,{WEBSTANDARDS}}
		<div class="box box---preview-script"><div class="box-inner">
			<h2>{!WEBSTANDARDS}</h2>

			<div>
				{WEBSTANDARDS}
			</div>
		</div></div>
	{+END}

	{+START,IF_NON_EMPTY,{HEALTH_CHECK}}
		<div class="box box---preview-script"><div class="box-inner">
			<h2>{!health_check:HEALTH_CHECK}</h2>

			<div>
				{+START,LOOP,HEALTH_CHECK}
					<p class="red_alert">
						{_loop_var}
					</p>
				{+END}
			</div>
		</div></div>
	{+END}

	{+START,IF_EMPTY,{WEBSTANDARDS}}
		<section class="box box---preview-script global-middle-faux"><div class="box-inner">
			<h2>{!PREVIEW}</h2>

			<div class="preview-box{+START,IF,{$AND,{$NOT,{$MOBILE,1}},{$MOBILE}}} mobile{+END}">
				<div id="preview-box-inner" class="preview-box-inner {+START,IF,{HAS_DEVICE_PREVIEW_MODES}}{+START,IF,{$AND,{$NOT,{$MOBILE,1}},{$MOBILE}}}js-preview-box-scroll{+END}{+END}">
					{$TRIM,{OUTPUT}}
				</div>
			</div>

			{+START,IF,{HAS_DEVICE_PREVIEW_MODES}}
				<hr class="spaced-rule" />

				<form target="_self" action="{$SELF_URL*,0,0,keep_mobile={$MOBILE}}" method="post">
					{$INSERT_FORM_POST_SECURITY}

					{HIDDEN}

					{+START,IF,{$NOT,{$MOBILE,1}}}
						{+START,IF,{$THEME_OPTION,mobile_support}}
							<p>
								<label for="mobile_version">{!MOBILE_VERSION}: <input type="checkbox" id="mobile_version" name="_mobile_version" data-click-pd="1" class="js-click-preview-mobile-button"{+START,IF,{$MOBILE}} checked="checked"{+END} /></label>
								{+START,IF,{$MOBILE}}
									&ndash; <em>{!USE_MOUSE_WHEEL_SCROLL}</em>
								{+END}
							</p>
						{+END}
					{+END}
				</form>
			{+END}
		</div></section>
	{+END}
</div>
