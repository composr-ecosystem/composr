{+START,IF_NON_EMPTY,{TEXT}}
	{$PARAGRAPH,{TEXT}}
{+END}

{$SET,form_name,form_{$RAND}}

{$REQUIRE_JAVASCRIPT,checking}

<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+START,IF,{$IN_STR,{FIELD_GROUPS},"file"}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END} target="_top" id="{$GET*,form_name}"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} data-submit-modsecurity-workaround="1"{+END} data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME={$GET,form_name}{+END}">
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

	<div>
		{FIELD_GROUPS}
	</div>

	{+START,INCLUDE,FORM_STANDARD_END}{+END}
</form>
