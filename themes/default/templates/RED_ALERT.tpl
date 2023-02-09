<{+START,IF_PASSED_AND_TRUE,INLINE}span{+END}{+START,IF_NON_PASSED_OR_FALSE,INLINE}div{+END} class="red-alert"{+START,IF_PASSED,ROLE} role="{ROLE*}"{+END}>
	{+START,INCLUDE,ICON}
		NAME=status/notice
		ICON_SIZE=24
		ICON_CLASS=red-alert-icon
	{+END}<span class="red-alert-message">{TEXT}</span>
</{+START,IF_PASSED_AND_TRUE,INLINE}span{+END}{+START,IF_NON_PASSED_OR_FALSE,INLINE}div{+END}>
