<div data-tpl="pointsSend">
	{+START,IF,{$NOT,{$HAS_PRIVILEGE,moderate_points}}}
		{$,Regular member}
		<p class="points-send-box-header">
			<span>{!SEND_TO,{$USERNAME*,{MEMBER},1}}</span>
			{+START,IF,{$CONFIG_OPTION,enable_gift_points}}
				{!SEND_TEXT_GIFT_POINTS,{VIEWER_GIFT_POINTS_BALANCE*},{VIEWER_POINTS_BALANCE*}}
			{+END}
			{+START,IF,{$NOT,{$CONFIG_OPTION,enable_gift_points}}}
				{!SEND_TEXT_POINTS,{VIEWER_POINTS_BALANCE*}}
			{+END}
		</p>

		<form title="{!SEND_POINTS}" method="post" class="js-submit-check-form" action="{SEND_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				<span class="send-fragment">
					<label for="send-amount">
						{!SEND}
					</label>
					<input maxlength="7" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="send-amount" class="form-control input-integer-required" placeholder="({!AMOUNT})" type="text" name="amount" />
					{!POINTS_L}
				</span>
				<span class="send-fragment">
					<label for="send-reason">
						{!POINTS_SEND_FOR}
					</label>
					<input maxlength="150" size="26" id="send-reason" class="form-control input-line-required" placeholder="({!REASON})" type="text" name="reason" />
				</span>
				<!--LAST-FIELD-->
				<p>
					<button id="send-points-submit" class="btn btn-primary buttons--points" type="submit">{!PROCEED_SHORT}</button>
					<label class="points-anon" for="send-anonymous">{!TICK_ANON}: <input type="checkbox" id="send-anonymous" name="anonymous" value="1" /></label>
				</p>
		</div>
		</form>
	{+END}

	{+START,IF,{$HAS_PRIVILEGE,moderate_points}}
		{$,Admin}
		<p class="points-send-box-header">
			<span>{!MODIFY_POINTS}</span>
			{+START,IF,{$CONFIG_OPTION,enable_gift_points}}
				{!SEND_TEXT_GIFT_POINTS,{VIEWER_GIFT_POINTS_BALANCE*},{VIEWER_POINTS_BALANCE*}}
			{+END}
			{+START,IF,{$NOT,{$CONFIG_OPTION,enable_gift_points}}}
				{!SEND_TEXT_POINTS,{VIEWER_POINTS_BALANCE*}}
			{+END}
		</p>

		<form title="{!SEND_POINTS}" method="post" class="js-submit-check-form" action="{SEND_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				<span class="send-fragment">
					<label for="trans_type" class="accessibility-hidden">
						{!POINTS_CHOOSE_ACTION}
					</label>
					<label for="send-amount" class="accessibility-hidden">
						{!AMOUNT}
					</label>
					<select id="trans_type" class="form-control js-click-check-send-options js-change-check-send-options" name="trans_type">
						<option value="">({!POINTS_CHOOSE_ACTION})</option>
						<option value="send">{!SEND_LONG}</option>
						<option value="credit">{!CREDIT_LONG}</option>
						<option value="debit">{!DEBIT_LONG}</option>
					</select>
					<input maxlength="7" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="send-amount" class="form-control input-integer-required" placeholder="({!AMOUNT})" type="text" name="amount" />
					{!POINTS_L}
				</span>
				<span class="send-fragment">
					<label for="send-reason">
						{!POINTS_SEND_FOR}
						<input maxlength="150" size="26" id="send-reason" class="form-control input-line-required" placeholder="({!REASON})" type="text" name="reason" />
					</label>
				</span>
				<!--LAST-FIELD-->
				<p>
					<button id="send-points-submit" class="btn btn-primary buttons--points" type="submit">{!PROCEED_SHORT}</button>
					<span id="points-anon-span" style="display: none;"><label class="points-anon" for="send-anonymous">{!TICK_ANON}: <input type="checkbox" id="send-anonymous" name="anonymous" value="1" /></label></span>
				</p>
			</div>
		</form>
	{+END}
</div>
