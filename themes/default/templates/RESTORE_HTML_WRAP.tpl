<!DOCTYPE html>

<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr}>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$CHARSET*}" />
		<link {$CSP_NONCE_HTML} href="restore.php?type=css" rel="stylesheet" />
		{+START,IF_NON_EMPTY,{CSS_NOCACHE}}
			<style {$CSP_NONCE_HTML}>
				{CSS_NOCACHE*}
			</style>
		{+END}
		<title>Backup restorer</title>
	</head>

	<body id="installer-body" class="website-body">
		<div class="container">
			<div class="installer-main">
				<img alt="Composr" width="550" height="115" src="{$BASE_URL*}/themes/default/images/EN/logo/standalone_logo.png" />
			</div>

			<div class="installer-main-inner">
				<div class="box box---restore-html-wrap"><div class="box-inner">
					{+START,IF_NON_PASSED_OR_FALSE,ERROR}
						<h1>Restoring the website</h1>
					{+END}

					{MESSAGE}
				</div></div>
			</div>
		</div>
	</body>
</html>
