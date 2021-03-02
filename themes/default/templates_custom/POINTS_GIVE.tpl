{$REQUIRE_JAVASCRIPT,idolisr}

<div data-tpl="pointsGive">
	{+START,SET,roles}
		<option value="">(Please select)</option>
		<option value="Helpful soul">Helpful soul</option>
		<option value="Support expert">Support expert</option>
		<option value="Programming god">Programming god</option>
		<option value="Themeing genius">Themeing genius</option>
		<option value="Community ambassador">Community ambassador</option>
	{+END}

	{+START,IF,{$NOT,{$HAS_ACTUAL_PAGE_ACCESS,admin_points}}}
		{$,Regular member}
		<p class="points-give-box-header">
			<span>{!GIVE_TO,{$USERNAME*,{MEMBER},1}}</span>
			{+START,IF_NON_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT,{VIEWER_GIFT_POINTS_AVAILABLE*}}
			{+END}
			{+START,IF_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT_UNLIMITED}
			{+END}
		</p>

		<form title="{!GIVE_POINTS}" method="post" class="js-submit-check-form" action="{GIVE_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<p>
				<label for="give-reason-pre">
					Their role
					<select id="give-reason-pre" class="form-control js-click-check-reason js-change-check-reason" name="reason_pre">
						{$GET,roles}
					</select>
				</label>:
			</p>

			<p>
				<label class="accessibility-hidden" for="give-reason">{!REASON}</label>
				<input maxlength="255" size="26" id="give-reason" class="form-control input-line-required" placeholder="{!REASON}" type="text" name="reason" />
			</p>

			<p>
				<label class="accessibility-hidden" for="give-amount">{!AMOUNT}</label>
				<input maxlength="8" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="give-amount" class="form-control input-integer-required" placeholder="{!AMOUNT}" type="text" name="amount" />

				<label class="points-anon" for="give-anonymous">{!TICK_ANON}: <input type="checkbox" id="give-anonymous" name="anonymous" value="1" /></label>

				<button id="give-points-submit" class="btn btn-primary btn-scri buttons--points" type="submit">{!GIVE_POINTS}</button>
			</p>
		</form>
	{+END}

	{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_points}}
		{$,Admin}
		<p class="points-give-box-header">
			<span>{!MODIFY_POINTS}</span>
			{+START,IF_NON_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT,{VIEWER_GIFT_POINTS_AVAILABLE*}}
			{+END}
			{+START,IF_EMPTY,{VIEWER_GIFT_POINTS_AVAILABLE}}
				{!GIVE_TEXT_UNLIMITED}
			{+END}
		</p>

		<form title="{!GIVE_POINTS}" method="post" class="js-submit-check-form" action="{GIVE_URL*}#tab--points">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				<div class="points-give-shared-options" style="margin-top: 0">
					<p>
						<label for="give-reason-pre">
							Their role
							<select id="give-reason-pre" class="form-control js-click-check-reason js-change-check-reason" name="reason_pre">
								{$GET,roles}
							</select>
						</label>:
					</p>

					<p>
						<label class="accessibility-hidden" for="give-reason">{!REASON}</label>
						<input maxlength="255" size="26" id="give-reason" class="form-control input-line-required" placeholder="{!REASON}" type="text" name="reason" />
					</p>

					<p>
						<label class="accessibility-hidden" for="give-amount">{!AMOUNT}</label>
						<input maxlength="8" data-prevent-input="[^\-\d{$BACKSLASH}{$DECIMAL_POINT*}]" size="7" id="give-amount" class="form-control input-integer-required" placeholder="{!AMOUNT}" type="text" name="amount" />

						<button id="give-points-submit" class="btn btn-primary buttons--points" type="submit">{!PROCEED_SHORT}</button>
					</p>
				</div>

				<div class="points-give-choices">
					<p class="points-give-choice-line first">
						<label for="trans-type-gift"><strong>{!GIVE_POINTS}</strong> <input checked="checked" type="radio" id="trans-type-gift" name="trans_type" value="gift" /></label> <span class="arr">&rarr;</span>&nbsp;
						<label class="sub-option points-anon" for="give-anonymous">{!TICK_ANON}: <input type="checkbox" id="give-anonymous" name="anonymous" value="1" /></label>
					</p>

					<p class="points-give-choice-line">
						<strong>{!MODIFY_POINTS}</strong> <span class="arr">&rarr;</span>&nbsp;
						<label class="sub-option" for="trans-type-charge">{!CHARGE} <input type="radio" id="trans-type-charge" name="trans_type" value="charge" /></label>
						<label class="sub-option" for="trans-type-refund">{!REFUND} <input type="radio" id="trans-type-refund" name="trans_type" value="refund" /></label>
					</p>
				</div>
			</div>
		</form>
	{+END}
</div>
