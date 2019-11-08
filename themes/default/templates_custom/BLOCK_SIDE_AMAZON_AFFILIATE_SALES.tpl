{$SET,height,{$ADD,{$MULT,{ITEMS_NUMBER},80},85}}

<div style="height: {$ADD*,20,{$GET,height}}px; text-align: center">
	<script {$CSP_NONCE_HTML}>
	amzn_assoc_ad_type = "responsive_search_widget";
	amzn_assoc_tracking_id = "{ASSOCIATES_ID;/}";
	amzn_assoc_marketplace = "amazon";
	amzn_assoc_region = "{REGION;/}";
	amzn_assoc_placement = "";
	amzn_assoc_search_type = "search_widget";
	amzn_assoc_width = "auto";
	amzn_assoc_height = "{$GET;/,height}";
	amzn_assoc_default_search_category = "{PRODUCT_LINE;/}";
	amzn_assoc_default_search_key = "{SUBJECT_KEYWORDS;/}";
	amzn_assoc_theme = "light";
	amzn_assoc_bg_color = "FFFFFF";
	</script>
	<script {$CSP_NONCE_HTML} src="//z-eu.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace={REGION*}"></script>
</div>
