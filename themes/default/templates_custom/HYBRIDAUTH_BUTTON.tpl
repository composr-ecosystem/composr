<form class="form-hybridauth-login" title="{!LOG_IN_WITH,{LABEL*}}" action="{$URL_FOR_GET_FORM*,{URL}}" method="get">
	{$HIDDENS_FOR_GET_FORM,{URL}}
	<button class="btn btn-secondary button--{CODENAME|} btn-hybridauth-login" type="submit">{+START,IF_NON_EMPTY,{ICON}}{+START,INCLUDE,ICON}NAME={ICON}
FORCE_COLOUR=1{+END} {+END}{!LOG_IN_WITH,{LABEL*}}</button>
</form>
