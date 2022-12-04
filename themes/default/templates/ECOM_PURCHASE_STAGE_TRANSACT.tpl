{$,Template is used for local payments only; may be embedded into shopping cart as well as serving in the purchasing module}

{+START,IF_PASSED,CONFIRMATION_BOX}
	<div class="box box---ecom-purchase-stage-transact"><div class="box-inner">
		{CONFIRMATION_BOX}
	</div></div>
{+END}

{+START,IF_PASSED,TEXT}
	{$PARAGRAPH,{TEXT}}
{+END}

{+START,IF_PASSED,FIELDS}
	<div class="local-payment-merchant-details-wrap"><div class="local-payment-merchant-details"><div>
		<div class="local-payment-transact-info">
			<p>{!TRANSACT_INFO}</p>
		</div>

		{+START,IF_NON_EMPTY,{PAYMENT_PROCESSOR_LINKS}}
			<div class="payment-processor-links">
				{PAYMENT_PROCESSOR_LINKS}
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{LOGOS}}
			<div class="local-payment-verified-account-logo">
				{LOGOS}
			</div>
		{+END}
	</div></div></div>

	<table class="map-table form-table wide-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				<col class="purchase-field-name-column" />
				<col class="purchase-field-input-column" />
			</colgroup>
		{+END}

		<tbody>
			{FIELDS}
		</tbody>
	</table>
{+END}

{+START,IF_PASSED,HIDDEN}
	{HIDDEN}
{+END}
