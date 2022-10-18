{TITLE}

{RESULTS_TABLE}

<form title="{!PRIMARY_PAGE_FORM}" method="get" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}">
	{$HIDDENS_FOR_GET_FORM,{$SELF_URL},product,id}

	<div>
		<p>
			<label for="type_code"><span class="field-name">{!PRODUCT}:</span> <select id="type_code" name="type_code" class="form-control">{PRODUCTS}</select></label>
			<label class="horiz-field-sep" for="purchase_id"><span class="field-name">{!PURCHASE_ID}:</span> <input maxlength="80" id="purchase_id" class="form-control" name="purchase_id" size="10" value="{PURCHASE_ID*}" type="text" /></label>
			<button data-disable-on-click="1" class="btn btn-primary btn-sm buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
		</p>
	</div>
</form>

<h2 class="force-margin">{!MORE} / {!ADVANCED}</h2>

<p class="lonely-label">
	{!ACTIONS}:
</p>
<nav>
	<ul class="actions-list">
		<li class="actions-list-strong">
			{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SELF:_SELF:export_transactions}">{!EXPORT_TRANSACTIONS}</a>
		</li>
	</ul>
</nav>
