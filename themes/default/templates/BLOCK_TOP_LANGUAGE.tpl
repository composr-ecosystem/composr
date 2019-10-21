<div class="top-button-wrapper" data-tpl="blockTopLanguage">
	<a id="top-language-button" class="top-button js-click-toggle-button-popup" href="#!">{+START,IF_NON_EMPTY,{CURRENT_LANG_COUNTRY_FLAG}}<img width="24" height="20" src="{$IMG*,flags_large/{$LCASE,{CURRENT_LANG_COUNTRY_FLAG}}}" alt="" />{+END}{+START,IF_EMPTY,{CURRENT_LANG_COUNTRY_FLAG}}{CURRENT_LANG*}{+END}</a>
	<div class="top-button-popup" id="top-language-rel" style="display: none">
		<div class="box box-arrow box--block-top-language"><span></span><div class="box-inner"><div>
			<div id="language-flags">
				{+START,LOOP,LANGS}
					<div>
						{+START,IF_NON_EMPTY,{COUNTRY_FLAG}}<a href="{$SELF_URL*,0,1,0,keep_lang={_loop_key&}}" hreflang="{$LCASE*,{_loop_key}}"><img width="24" height="20" src="{$IMG*,flags_large/{$LCASE,{COUNTRY_FLAG}}}" alt="" /></a>{+END}
						<a{+START,IF,{$EQ,{CURRENT_LANG},{_loop_key}}} class="current" title="{!CURRENT} {!LANGUAGE}: {CURRENT_LANG_FULL_NAME*}"{+END} href="{$SELF_URL*,0,1,0,keep_lang={_loop_key&}}" hreflang="{$LCASE*,{_loop_key}}">{FULL_NAME*}</a>
					</div>
				{+END}
			</div>
		</div></div></div>
	</div>
</div>
