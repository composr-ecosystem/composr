<div data-tpl="pointsGive">
	{+START,IF,{$NOT,{$HAS_ACTUAL_PAGE_ACCESS,admin_points}}}
		{$,Regular member}
		<p class="points-give-box-header">
			<span>{!GIVE_TO,{$USERNAME*,{MEMBER},1}}</span>
			{+START,IF_NON_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT,{VIEWER_GIFT_POINTS_AVAILABLE*},{$?,{$CONFIG_OPTION,enable_gift_points},{!GIFT_POINTS_L},{!POINTS_L}}}
			{+END}
			{+START,IF_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT_UNLIMITED,{$?,{$CONFIG_OPTION,enable_gift_points},{!GIFT_POINTS_L},{!POINTS_L}}}
			{+END}
		</p>

		<form title="{!GIVE_POINTS}" method="post" class="js-submit-check-form" action="{GIVE_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				<span class="give-fragment">
					<label for="give-amount">
						{!GIVE}
					</label>
						<input maxlength="7" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="give-amount" class="form-control input-integer-required" placeholder="({!AMOUNT})" type="text" name="amount" />
						{!POINTS_L}
				</span>
				<span class="give-fragment">
					<label for="give-reason">
						{!POINTS_GIVE_FOR}
					</label>
					<input maxlength="150" size="26" id="give-reason" class="form-control input-line-required" placeholder="({!REASON})" type="text" name="reason" />
				</span>
				<!--LAST-FIELD-->
				<p>
					<button id="give-points-submit" class="btn btn-primary buttons--points" type="submit">{!PROCEED_SHORT}</button>
					{+START,IF,{$HAS_PRIVILEGE,have_negative_gift_points}}
						<span id="points-payee-span" style="display: none;">
							<label for="trans_payee">
								{!PAYEE}
							</label>
							<select id="trans_payee" class="form-control" name="trans_payee">
								<option value="me">{$USERNAME*}</option>
								<option value="website">{$SITE_NAME*}</option>
							</select>
						</span>
					{+END}
					<label class="points-anon" for="give-anonymous">{!TICK_ANON}: <input type="checkbox" id="give-anonymous" name="anonymous" value="1" /></label>
				</p>
		</div>
		</form>
	{+END}

	{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_points}}
		{$,Admin}
                <p class="points-give-box-header">
			<span>{!MODIFY_POINTS}</span>
			{+START,IF_NON_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT,{VIEWER_GIFT_POINTS_AVAILABLE*},{$?,{$CONFIG_OPTION,enable_gift_points},{!GIFT_POINTS_L},{!POINTS_L}}}
			{+END}
			{+START,IF_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT_UNLIMITED,{$?,{$CONFIG_OPTION,enable_gift_points},{!GIFT_POINTS_L},{!POINTS_L}}}
			{+END}
		</p>

		<form title="{!GIVE_POINTS}" method="post" class="js-submit-check-form" action="{GIVE_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				<span class="give-fragment">
					<label for="trans_type" class="accessibility-hidden">
						{!POINTS_CHOOSE_ACTION}
					</label>
					<select id="trans_type" class="form-control js-click-check-gift-options js-change-check-gift-options" name="trans_type">
						<option value="">({!POINTS_CHOOSE_ACTION})</option>
						<option value="gift">{!GIVE}</option>
						<option value="charge">{!CHARGE}</option>
						<option value="refund">{!REFUND}</option>
					</select>
					<input maxlength="7" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="give-amount" class="form-control input-integer-required" placeholder="({!AMOUNT})" type="text" name="amount" />
					{!POINTS_L}
				</span>
				<span class="give-fragment">
					<label for="give-reason">
						{!POINTS_GIVE_FOR}
						<input maxlength="150" size="26" id="give-reason" class="form-control input-line-required" placeholder="({!REASON})" type="text" name="reason" />
					</label>
				</span>
				<!--LAST-FIELD-->
				<p>
					<button id="give-points-submit" class="btn btn-primary buttons--points" type="submit">{!PROCEED_SHORT}</button>
					{+START,IF,{$HAS_PRIVILEGE,have_negative_gift_points}}
						<span id="points-payee-span" style="display: none;">
							<label for="trans_type">
								{!PAYEE}
							</label>
							<select id="trans_payee" class="form-control" name="trans_payee">
								<option value="me">{$USERNAME*}</option>
								<option value="website">{$SITE_NAME*}</option>
							</select>
						</span>
					{+END}
					<span id="points-anon-span" style="display: none;"><label class="points-anon" for="give-anonymous">{!TICK_ANON}: <input type="checkbox" id="give-anonymous" name="anonymous" value="1" /></label></span>
				</p>
			</div>
		</form>
	{+END}
</div>
