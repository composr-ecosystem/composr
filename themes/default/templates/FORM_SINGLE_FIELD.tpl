{+START,IF_NON_EMPTY,{TEXT}}
	{$PARAGRAPH,{TEXT}}
{+END}

{$REQUIRE_JAVASCRIPT,checking}

<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+START,IF,{$IN_STR,{FIELD},"file"}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}{+START,IF_NON_PASSED,TARGET} target="_top"{+END} class="form-table">
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

	<div>
		{HIDDEN}

		{+START,IF_PASSED,NAME}{+START,IF_PASSED,LABEL}
			<label class="accessibility-hidden" for="{NAME*}">{!LABEL}</label>
		{+END}{+END}

		{FIELD}

		{+START,IF_NON_EMPTY,{SUBMIT_NAME}}
			{+START,INCLUDE,FORM_STANDARD_END}{+END}
		{+END}
	</div>
</form>
