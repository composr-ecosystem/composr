{$,It is advisable to edit this MAIL template in the default theme, as this will ensure that all mail sent from the website will be formatted consistently, whatever theme happens to be running at the time}

<!DOCTYPE html>
<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr}>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$LCASE*,{$CHARSET}}" />
<title>{TITLE*}</title>
{CSS}
</head>
<body style="font-size: 12px" class="email-body">
	<div style="font-size: 12px" class="email-body">
		<p class="email-logo">
			<a href="{$BASE_URL*}"><img src="{$IMG*,logo/standalone_logo}" title="{$SITE_NAME*}" alt="{$SITE_NAME*}" /></a>
		</p>

		<h2>{TITLE*}</h2>

		{CONTENT}

		<hr class="spaced-rule" />

		<div class="email-footer">
			<div class="email-copyright">
				{$COPYRIGHT`}
			</div>

			<div class="email-url">
				{$PREG_REPLACE*,^.*://,,{$BASE_URL}}
			</div>

			<div class="email-url">
				<a href="{$FIND_SCRIPT*,unsubscribe}">{!UNSUBSCRIBE}</a>
			</div>
		</div>
		<br clear="all" />
	</div>
</body>
</html>
