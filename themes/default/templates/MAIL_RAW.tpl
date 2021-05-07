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
</body>
</html>
