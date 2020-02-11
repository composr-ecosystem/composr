<form title="{!MAKE_PAYMENT}" class="ecommerce-button" action="{FORM_URL*}" method="post">
	<input type="hidden" name="trans_id" value="{TRANS_EXPECTING_ID*}" />
	<input type="hidden" name="merchant" value="{USERNAME*}" />
	<input type="hidden" name="digest" value="{DIGEST*}" />
	<input type="hidden" name="amount" value="{AMOUNT*}" />
	<input type="hidden" name="callback" value="{$FIND_SCRIPT*,ecommerce}?from=secpay&amp;type_code={TYPE_CODE*}" />
	<input type="hidden" name="currency" value="{CURRENCY*}" />
	<input type="hidden" name="cb_post" value="true" />
	<input type="hidden" name="req_cv2" value="true" />
	{+START,IF,{TEST_MODE}}
		<input type="hidden" name="test_status" value="true" />
	{+END}
	<input type="hidden" name="md_flds" value="trans_id:req_cv2" />
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
		<button id="purchase-button" data-disable-on-click="1" class="btn btn-primary btn-scr menu--rich-content--ecommerce--purchase" type="submit">{+START,INCLUDE,ICON}NAME=menu/rich_content/ecommerce/purchase{+END} {!MAKE_PAYMENT}</button>
	</div>
</form>
