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
				<p>{$COPYRIGHT`}</p>
				{+START,IF_NON_EMPTY,{VIEW_IN_BROWSER}}
					<p>{VIEW_IN_BROWSER}</p>
					<p>{!mail:VIEW_MAIL_IN_BROWSER_2}</p>
				{+END}
			</div>

			<div class="email-url">
				<p>{$PREG_REPLACE*,^.*://,,{$BASE_URL}}</p>
				<p><a href="{$FIND_SCRIPT*,unsubscribe}">{!UNSUBSCRIBE}</a></p>
			</div>
		</div>
		<br clear="all" />
	</div>
</body>
</html>
