<form data-tpl="massSelectDeleteForm" title="{!DELETE_SELECTION}" id="mass-select-form--{$GET%,support_mass_select}" style="display: none" class="mass-delete-form" action="{$PAGE_LINK*,_SEARCH:{$GET,support_mass_select}:mass_delete:redirect={$SELF_URL&}}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	<p class="proceed-button">
		<button class="btn btn-danger btn-scri" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE_SELECTION}</span></button>
	</p>
</form>
