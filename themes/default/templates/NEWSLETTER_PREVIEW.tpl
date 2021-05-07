{$REQUIRE_JAVASCRIPT,newsletter}

<iframe id="preview-frame" name="preview-frame" title="{!PREVIEW}" src="{$BASE_URL*}/data/empty.php" data-tpl="newsletterPreview" data-tpl-params="{+START,PARAMS_JSON,HTML_PREVIEW}{_*}{+END}"{$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"}>{!PREVIEW}</iframe>
