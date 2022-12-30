{TITLE}

{$REQUIRE_CSS,messages}

<div class="site-special-message ssm-inform" role="alert"{+START,IF,{$DESKTOP}}{+START,IF_PASSED,IMAGE_URL} style="background-image: url('{IMAGE_URL;*}');"{+END}{+END}>
	<div class="site-special-message-inner">
		<div class="box box---inform-screen"><div class="box-inner">
			{+START,INCLUDE,ICON}
				NAME=status/inform
				ICON_SIZE=24
				ICON_CLASS=global-message-icon
			{+END}

			{TEXT*}
		</div></div>
	</div>
</div>

{+START,IF_PASSED,BACK_URL}
<form class="back-button" title="{!NEXT_ITEM_BACK}" action="{BACK_URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	<div>
		{+START,IF_PASSED,FIELDS}{FIELDS}{+END}
		<button class="button-icon" type="submit" title="{!NEXT_ITEM_BACK}">
			{+START,INCLUDE,ICON}
				NAME=admin/back
				ICON_SIZE=48
			{+END}
		</button>
	</div>
</form>
{+END}
