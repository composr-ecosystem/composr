<!DOCTYPE html>
<html lang="{$LCASE*,{$LANG}}"{$ATTR_DEFAULTED,dir,{!dir},ltr}>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$LCASE*,{$CHARSET}}" />
<title>{TITLE*}</title>
{CSS}
</head>
<body style="font-size: 12px"{+START,IF_PASSED_AND_TRUE,SOME_STYLE} class="email-body"{+END}>
	<div style="font-size: 12px">
		{CONTENT}
	</div>
	<hr class="spaced-rule" />
	<div class="email-footer">
		<div class="email-url">
			<a href="{$FIND_SCRIPT*,unsubscribe}">{!UNSUBSCRIBE}</a>
		</div>
	</div>
	<br clear="all" />
</body>
</html>
