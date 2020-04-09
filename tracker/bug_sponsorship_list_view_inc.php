<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This include file prints out the list of users sponsoring the current
 * bug.	$f_bug_id must be set to the bug id
 *
 * @package MantisBT
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses access_api.php
 * @uses bug_api.php
 * @uses collapse_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses current_user_api.php
 * @uses form_api.php
 * @uses helper_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses sponsorship_api.php
 * @uses utility_api.php
 */

if( !defined( 'BUG_SPONSORSHIP_LIST_VIEW_INC_ALLOW' ) ) {
	return;
}

require_api( 'access_api.php' );
require_api( 'bug_api.php' );
require_api( 'collapse_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'current_user_api.php' );
require_api( 'form_api.php' );
require_api( 'helper_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'sponsorship_api.php' );
require_api( 'utility_api.php' );

#
# Determine whether the sponsorship section should be shown.
#

if( ( config_get( 'enable_sponsorship' ) == ON ) && ( access_has_bug_level( config_get( 'view_sponsorship_total_threshold' ), $f_bug_id ) ) ) {
	$t_sponsorship_ids = sponsorship_get_all_ids( $f_bug_id );

	$t_sponsorships_exist = count( $t_sponsorship_ids ) > 0;
	//$t_can_sponsor = !bug_is_readonly( $f_bug_id ) && !current_user_is_anonymous();
	$t_can_sponsor = !bug_is_readonly( $f_bug_id ) && !current_user_is_anonymous() && $sponsorship_open;

	$t_show_sponsorships = $t_sponsorships_exist || $t_can_sponsor;
} else {
	$t_show_sponsorships = false;
}

#
# Sponsorship Box
#

if( $t_show_sponsorships ) {
?>

<a id="sponsorships"></a> <br />

<?php
	collapse_open( 'sponsorship' );
?>

<table class="width100" cellspacing="1">
	<tr>
		<!--
		<td width="50" rowspan="3">
			<i class="fa fa-usd" title="<?php echo lang_get( 'sponsor_verb' ) ?>"></i>
		</td>
		<td class="form-title" colspan="2">
		-->
		<!-- Composr - changing how this looks a bit -->
		<td width="20" rowspan="5">
		</td>
		<td class="form-title" colspan="2"><br /><?php

			collapse_icon( 'sponsorship' );
			echo lang_get( 'users_sponsoring_bug' );

			$t_details_url = lang_get( 'sponsorship_process_url' );
			if( !is_blank( $t_details_url ) ) {
				echo '&#160;[<a href="' . $t_details_url . '">'
					. lang_get( 'sponsorship_more_info' ) . '</a>]';
			}
		?>

			<!-- Composr - adding some spacing -->
			<br />
		</td>
	</tr>

<?php
	// Composr - sponsorship improvements
	$f_hours = floatval($hours);
	$f_credits_per = floatval($cms_sc_credits_per_hour);
	$f_price_per = floatval($cms_sc_price_per_credit);
	$f_hours_by_credits = $f_hours * $f_credits_per;
	$cash_needed = $f_hours * $f_credits_per * $f_price_per;
	$cash_needed_string = $cms_sc_main_currency_symbol . number_format($cash_needed) . ' ' . strtoupper($cms_sc_main_currency);
	$cms_sc_main_currency = strtoupper($cms_sc_main_currency);
	$alternate_currencies_links = '';
	if (!empty($cms_sc_alternate_currencies)) {
		if (is_array($cms_sc_alternate_currencies)) {
			$cur_count = count($cms_sc_alternate_currencies);
			$z = 1;
			foreach ($cms_sc_alternate_currencies as $v) {
				$alternate_currencies_links .= '<a href="http://www.xe.com/ucc/convert/?Amount=' . strval($cash_needed) . '&amp;From=' . $cms_sc_main_currency . '&amp;To=' . strtoupper($v) . '" target="_blank">' . strtoupper($v) . '</a>';
				if ($z !== $cur_count) {
					$alternate_currencies_links .= ',&nbsp; ';
				}
				$z++;
			}
			unset($z);
		} else {
			$alternate_currencies_links .= '<a href="http://www.xe.com/ucc/convert/?Amount=' . strval($cash_needed) . '&amp;From=' . $cms_sc_main_currency . '&amp;To=' . strtoupper($cms_sc_alternate_currencies) . '" target="_blank">&nbsp;' . strtoupper($cms_sc_alternate_currencies) . ' etc</a>';
		}
	} else {
		switch ($cms_sc_main_currency) {
			case 'EUR':
				$cms_sc_alternate_currencies = 'USD';
				break;
			case 'GBP':
				$cms_sc_alternate_currencies = 'USD';
				break;
			case 'USD':
				$cms_sc_alternate_currencies = 'EUR';
				break;
			case 'CAD':
				$cms_sc_alternate_currencies = 'USD';
				break;
			default:
				$cms_sc_alternate_currencies = 'EUR';
				break;
		}
		$alternate_currencies_links .= '<a href="http://www.xe.com/ucc/convert/?Amount=' . strval($cash_needed) . '&amp;From=' . $cms_sc_main_currency . '&amp;To=' . $cms_sc_alternate_currencies  . '" target="_blank">&nbsp;' . $cms_sc_alternate_currencies  . ' etc</a>';
	}

	if( $t_can_sponsor ) {

		// Composr - sponsorship improvements...

		// Find member's credits
		if ($cms_sc_multi_lang_content) {
			$sql = 'SELECT f.id FROM ' . $cms_sc_db_prefix . 'f_custom_fields f JOIN ' . $cms_sc_db_prefix . 'translate t ON t.id=f.cf_name WHERE text_original=\'' . $cms_sc_custom_profile_field . '\'';
		} else {
			$sql = 'SELECT f.id FROM ' . $cms_sc_db_prefix . 'f_custom_fields f WHERE f.cf_name=\'' . $cms_sc_custom_profile_field . '\'';
		}
		$result = db_query($sql, array());
		$field_num = db_fetch_array($result);
		$sql = 'SELECT field_' . strval($field_num['id']) . ' AS result FROM ' . $cms_sc_db_prefix . 'f_member_custom_fields WHERE mf_member_id=' . auth_get_current_user_id();
		$result = db_query($sql, array());
		$num_credits = db_fetch_array($result);
		$credits_available = isset($num_credits['result']) ? $num_credits['result'] : 0;
		if ($credits_available == '') $credits_available = 0;

		// Find how much the user can sponsor
		$result = db_query('SELECT SUM(amount) AS result FROM mantis_sponsorship_table s JOIN mantis_bug_table b ON s.bug_id=b.id WHERE status<80 AND user_id=' . auth_get_current_user_id(), array());
		$amount_sponsored = db_fetch_array($result);
		$total_user_sponsored = isset($amount_sponsored['result'])?$amount_sponsored['result']:0;
		if ($total_user_sponsored == '') $total_user_sponsored = 0;
		$credits_available_real = $credits_available;
		$credits_sponsored = intval(round($total_user_sponsored/$f_price_per));
		$credits_available -= $credits_sponsored;

		// Find the sponsorship so far
		$result = db_query('SELECT SUM(amount) AS result FROM mantis_sponsorship_table WHERE bug_id=' . $f_bug_id, array());
		$amount_sponsored = db_fetch_array($result);
		$t_total_sponsorship = isset($amount_sponsored['result']) ? $amount_sponsored['result'] : 0.0;
		if ($t_total_sponsorship == '') $t_total_sponsorship = 0.0;
		$t_total_sponsorship_confirmed = 0.0;

?>
	<tr>
		<th class="category" width="15%"><?php echo lang_get( 'sponsor_issue' ) . /*Composr - sponsorship improvements*/lang_get('cms_divide_cost') ?></th>
		<td>
			<!-- Composr - sponsorship improvements -->
			<?php if (($cms_sponsorship_locked_until !== null) && ($cms_sponsorship_locked_until > time())) { ?>
				<p><?php echo sprintf(lang_get('cms_sponsorship_is_disabled'), $cms_sc_site_url . '/contact/sponsor.htm'); ?></p>
			<?php } else { ?>

			<form method="post" action="bug_set_sponsorship.php">
				<!-- Composr - sponsorship improvements -->
				<?php if ($hours != 0) { ?>
				<p><?php echo sprintf(lang_get('cms_sponsor_first_message'), intval($f_hours), intval($f_hours_by_credits), $cash_needed_string, $alternate_currencies_links, $cms_sc_business_name, $cms_sc_product_name);?></p>
				<?php } ?>
				<p><?php echo sprintf(lang_get('cms_sponsor_second_message'), $cms_sc_site_url . '/contact/sponsor.htm'); ?></p>

				<?php echo form_security_field( 'bug_set_sponsorship' ) ?>
				<!--Composr - disabled <?php echo sponsorship_get_currency() ?>-->
				<input type="hidden" name="bug_id" value="<?php echo $f_bug_id ?>" size="4" />
				<!--Composr - disabled <input type="text" name="amount" class="input-sm" value="<?php echo config_get( 'minimum_sponsorship_amount' )  ?>" size="4" />-->

				<!-- Composr - sponsorship improvements -->
				<p>
					<label><input type="text" id="sponsor_amount_credits" name="amount_credits" data-price-per="<?php echo $f_price_per; ?>" value="" size="20" /> <?php echo lang_get('cms_amount_in_support_credits'); ?></label> <?php echo sprintf(lang_get('cms_current_credits_balance'), $credits_available)?> <br />
					<label><input type="text" id="sponsor_amount" name="amount" data-price-per="<?php echo $f_price_per; ?>" value="" size="20" /> <?php echo sprintf(lang_get('cms_amount_in_main_currency'), $cms_sc_main_currency)?></label>
				</p>
				<p>

				<input type="submit" class="btn btn-primary btn-white btn-round" name="sponsor" value="<?php echo lang_get( 'sponsor_verb' ) ?>" />

				<!-- Composr - sponsorship improvements -->
				</p>
				<?php
				foreach ( $t_sponsorship_ids as $id ) {
					$t_sponsorship = sponsorship_get( $id );
					if ($t_sponsorship->user_id==auth_get_current_user_id()) {
				?>
				<p>
					<?php echo lang_get('cms_filling_in_form_message');?>
				</p>
				<?php
					}
				}
				?>

			</form>

			<!-- Composr - sponsorship improvements -->
			<?php } ?>
		</td>
	</tr>
<?php
	}

	//$t_total_sponsorship = bug_get_field( $f_bug_id, 'sponsorship_total' );	Composr - calculated in more sophisticated way above
	if( $t_total_sponsorship > 0 ) {
?>
	<tr>
		<th class="category" width="15%"><?php echo lang_get( 'sponsors_list' ) ?></th>
		<td>
		<?php
			/* Composr - sponsorship improvements echo sprintf( lang_get( 'total_sponsorship_amount' ),
				sponsorship_format_amount( $t_total_sponsorship ) );
			if( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
			*/

			// Composr - sponsorship improvements echo '<br /><br />';
			$i = 0;
			foreach ( $t_sponsorship_ids as $t_id ) {
				$t_sponsorship = sponsorship_get( $t_id );
				$t_date_added = date( config_get( 'normal_date_format' ), $t_sponsorship->date_submitted );

				if( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
					echo ($i > 0) ? '<br />' : '';
					$i++;

					echo sprintf( lang_get( 'label' ), $t_date_added ) . lang_get( 'word_separator' );
					print_user( $t_sponsorship->user_id );
					// Composr - sponsorship improvements echo ' (' . sponsorship_format_amount( $t_sponsorship->amount ) . ')';
					echo ' - ' , sponsorship_format_amount( $t_sponsorship->amount ) , ' ('.round(floatval($t_sponsorship->amount)/$cms_sc_price_per_credit).' '.lang_get('cms_support_credits').' )';
					if( access_has_bug_level( config_get( 'handle_sponsored_bugs_threshold' ), $f_bug_id ) ) {
						echo ' ' . get_enum_element( 'sponsorship', $t_sponsorship->paid );
					}
				}

				// Composr - sponsorship improvements...

				// Find how many credits they currently have
				if ($cms_sc_multi_lang_content) {
					$sql = 'SELECT f.id FROM ' . $cms_sc_db_prefix . 'f_custom_fields f JOIN ' . $cms_sc_db_prefix . 'translate t ON t.id=f.cf_name WHERE text_original=\'' . $cms_sc_custom_profile_field . '\'';
				} else {
					$sql = 'SELECT f.id FROM ' . $cms_sc_db_prefix . 'f_custom_fields f WHERE f.cf_name=\'' . $cms_sc_custom_profile_field . '\'';
				}
				$result = db_query($sql, array());
				$field_num = db_fetch_array($result);
				$sql = 'SELECT field_' . strval($field_num['id']) . ' AS result FROM ' . $cms_sc_db_prefix . 'f_member_custom_fields WHERE mf_member_id=' . $t_sponsorship->user_id;
				$result = db_query($sql, array());
				$num_credits = db_fetch_array($result);

				// Enough or not?
				if ($num_credits['result'] * $cms_sc_price_per_credit >= $t_sponsorship->amount) {
					if( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
						echo lang_get('cms_backed_by_existing_support_credits');
					}
					$t_total_sponsorship_confirmed += $t_sponsorship->amount;
				} else {
					if( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
						echo lang_get('cms_not_backed_by_existing_support_credits');
						if ($t_sponsorship->user_id == auth_get_current_user_id()) {
							echo sprintf(lang_get('cms_buy_some'), $cms_sc_commercial_support_url);
						}
					}
				}
			}
		?>
		</td>
		</tr>

		<!-- Composr - sponsorship improvements... -->

		<tr>
			<th class="category" width="15%"><?php echo lang_get('cms_progress_theory')?></th>
			<td>
				<progress style="width: 100%" value="<?php echo $t_total_sponsorship; ?>" max="<?php echo $cash_needed; ?>"></progress>
				<?php echo round(100 * $t_total_sponsorship/$cash_needed) . '% (' . round(($cash_needed - $t_total_sponsorship) / $f_price_per) . ' ' . lang_get('cms_credits_remaining') . ')'; ?>
			</td>
		</tr>
		<?php if ($t_bug->status != 80) { ?>
		<tr>
			<th class="category" width="15%"><?php echo lang_get('cms_progress_paid_up')?></th>
			<td>
				<progress style="width: 100%" value="<?php echo $t_total_sponsorship_confirmed; ?>" max="<?php echo $cash_needed; ?>"></progress>
				<?php echo round(100 * $t_total_sponsorship_confirmed / $cash_needed) . '% (' . round(($cash_needed - $t_total_sponsorship_confirmed) / $f_price_per) . ' ' . lang_get('cms_credits_remaining') . ')'; ?>
			</td>
		</tr>
		<?php } ?>

<?php
		}
?>
</table>

<?php
	collapse_closed( 'sponsorship' );
?>

<table class="width100" cellspacing="1">
	<tr>
		<td class="form-title"><?php
			collapse_icon( 'sponsorship' );
			echo lang_get( 'users_sponsoring_bug' );

			$t_details_url = lang_get( 'sponsorship_process_url' );
			if( !is_blank( $t_details_url ) ) {
				echo '&#160;[<a href="' . $t_details_url . '">'
					. lang_get( 'sponsorship_more_info' ) . '</a>]';
			}

			if( $t_total_sponsorship > 0 ) {
				echo ' <span style="font-weight: normal;">(';
				echo sprintf( lang_get( 'total_sponsorship_amount' ),
				sponsorship_format_amount( $t_total_sponsorship ) );
				echo ')</span>';
			}
?>
		</td>
	</tr>
</table>

<?php
	collapse_end( 'sponsorship' );
} # If sponsorship enabled
