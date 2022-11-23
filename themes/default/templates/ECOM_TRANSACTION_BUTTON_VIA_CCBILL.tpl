<form title="{!MAKE_PAYMENT}" class="ecommerce-button" action="{FORM_URL*}" method="get">
	<input type="hidden" name="clientAccnum" value="{ACCOUNT_NUM*}" /> {$,An integer value representing the 6-digit client account number.}
	<input type="hidden" name="clientSubacc" value="{SUBACCOUNT_NUM*}" /> {$,An integer value representing the 4-digit client subaccount number.}
	<input type="hidden" name="formName" value="{FORM_NAME*}" /> {$,The name of the form.}
	<input type="hidden" name="initialPrice" value="{AMOUNT*}" /> {$,A decimal value representing the initial price.}
	<input type="hidden" name="initialPeriod" value="{FORM_PERIOD*}" /> {$,An integer representing the length, in days, of the initial billing period.}
	<input type="hidden" name="currencyCode" value="{CURRENCY*}" /> {$,An integer representing the 3-digit currency code that will be used for the transaction.}
	<input type="hidden" name="formDigest" value="{DIGEST*}" /> {$,An MD5 Hex Digest based on the above values}
	<input type="hidden" name="productDesc" value="{ITEM_NAME*}" /> {$,Hopefully shown to the customer when paying}
	<input type="hidden" name="customPurchaseId" value="{TRANS_EXPECTING_ID*}" /> {$,Custom variable for tracking purchase}
	<input type="hidden" name="customItemName" value="{ITEM_NAME*}" /> {$,Custom variable for tracking item name}
	<input type="hidden" name="customIsSubscription" value="0" /> {$,Custom variable for subscription status}
	{+START,IF_NON_EMPTY,{MEMBER_ADDRESS}}
		{+START,LOOP,MEMBER_ADDRESS}
			{+START,IF_NON_EMPTY,{_loop_key}}
				{+START,IF_NON_EMPTY,{_loop_var}}
					<input type="hidden" name="{_loop_key*}" value="{_loop_var*}" />
				{+END}
			{+END}
		{+END}
	{+END}

	<div class="purchase-button">
		<button data-disable-on-click="1" type="submit">{!MAKE_PAYMENT}</button>
	</div>
</form>
