<!DOCTYPE html>

{$SET,page_link_privacy,{$PAGE_LINK,:privacy}}

<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr} data-view="Global" data-view-params="{+START,PARAMS_JSON,page_link_privacy}{_*}{+END}" class="in-minikernel-version">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$CHARSET*}" />
		<meta name="GENERATOR" content="Composr" />
		<meta name="description" content="Composr installer" />
		<link rel="icon" href="{$BRAND_BASE_URL*}/favicon.ico" type="image/x-icon" />
		<link {$CSP_NONCE_HTML} href="{CSS_URL*}" rel="stylesheet" />
		<link {$CSP_NONCE_HTML} href="{CSS_URL_2*}" rel="stylesheet" />
		<style {$CSP_NONCE_HTML}>
			{CSS_NOCACHE*}
		</style>
		<title>{!INSTALLER,Composr}</title>

		<meta name="robots" content="noindex, nofollow" />

		{+START,INCLUDE,HTML_HEAD_POLYFILLS}{+END}
	</head>

	<body id="installer-body" class="website-body" data-tpl="installerHtmlWrap" data-tpl-params="{+START,PARAMS_JSON,DEFAULT_FORUM}{_*}{+END}">
		<div class="container">
			<div class="installer-main">
				<img alt="Composr" src="{LOGO_URL*}" width="550" height="115" />
			</div>

			<div class="installer-main-inner">
				<div class="box box---installer-html-wrap">
					<div class="box-inner">
						<h1>{!INSTALLER,Composr}: {!INSTALLER_STEP,{STEP},10}</h1>

						<div id="extra-errors"></div>

						<div>
							{CONTENT}
						</div>
					</div>
				</div>
			</div>

			<div class="installer-version">
				<p>
					{!VERSION_NUM,{VERSION}}
					<br />
					Composr, {!version:CREATED_BY,ocProducts}
				</p>
				<p>
					<a target="_blank" title="compo.sr {!LINK_NEW_WINDOW}" href="{$BRAND_BASE_URL*}">{$BRAND_BASE_URL*}</a>
				</p>
			</div>

			<script {$CSP_NONCE_HTML}>
				{+START,IF_PASSED,INSTALLER_JS}{INSTALLER_JS/}{+END}
			</script>
		</div>
	</body>
</html>
