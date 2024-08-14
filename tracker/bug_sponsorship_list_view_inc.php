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
	$t_can_sponsor = !bug_is_readonly( $f_bug_id ) && !bug_is_resolved($f_bug_id) && !bug_is_closed($f_bug_id) && !current_user_is_anonymous();

	$t_show_sponsorships = $t_can_sponsor;
} else {
	$t_show_sponsorships = false;
}

#
# Sponsorship Box
#
?>

    <div class="col-md-12 col-xs-12">
        <div class="space-10"></div>
        <a id="sponsorships"></a>
        <?php
            $t_collapse_block = is_collapsed( 'sponsorships' );
            $t_block_css = $t_collapse_block ? 'collapsed' : '';
            $t_block_icon = $t_collapse_block ? 'fa-chevron-down' : 'fa-chevron-up';
        ?>
        <div id="sponsorships" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
            <div class="widget-header widget-header-small">
                <h4 class="widget-title lighter">
                    <?php print_icon( 'fa-usd', 'ace-icon' ); ?>
                    <?php echo lang_get( 'sponsor_verb' ) ?>
                </h4>
                <div class="widget-toolbar">
                    <a data-action="collapse" href="#">
                        <?php print_icon( $t_block_icon, '1 ace-icon bigger-125' ); ?>
                    </a>
                </div>
            </div>
            <div class="widget-body">
                <?php
                if( $t_show_sponsorships ) {
                ?>
                <div class="widget-toolbox padding-8 clearfix">
                    <p>
                        <?php
                            echo lang_get( 'users_sponsoring_bug' );
                            $t_details_url = lang_get( 'sponsorship_process_url' );
                            if( !is_blank( $t_details_url ) ) {
                                echo '&#160;[<a href="' . $t_details_url . '">'
                                    . lang_get( 'sponsorship_more_info' ) . '</a>]';
                            }
                        ?>
                    </p>
                    <form method="post" action="bug_set_sponsorship.php" class="form-inline noprint">
                        <?php echo form_security_field( 'bug_set_sponsorship' ) ?>
                        <input type="hidden" name="bug_id" value="<?php echo $f_bug_id ?>" size="4" />
                        <input type="text" name="amount" class="input-sm" value="<?php echo config_get( 'minimum_sponsorship_amount' )  ?>" size="4" />
                        <?php echo sponsorship_get_currency() ?>
                        <input type="submit" class="btn btn-primary btn-white btn-round" name="sponsor" value="<?php echo lang_get( 'sponsor_verb' ) ?>" />
                    </form>
                </div>
                <?php
                    }
                ?>
                    <div class="widget-main no-padding">
                        <?php
                            if( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th class="small-caption">Date Added</th>
                                            <th class="small-caption">Member</th>
                                            <th class="small-caption">Amount Sponsored</th>
                                            <?php
                                                if( access_has_bug_level( config_get( 'handle_sponsored_bugs_threshold' ), $f_bug_id ) ) {
                                            ?>
                                                <th class="small-caption">Status</th>
                                            <?php
                                                }
                                            ?>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                        $i = 0;
                                        foreach ( $t_sponsorship_ids as $t_id ) {
                                            $t_sponsorship = sponsorship_get( $t_id );
                                            $t_date_added = date( config_get( 'normal_date_format' ), $t_sponsorship->date_submitted );

                                            echo '<tr>';
                                            $i++;

                                            echo '<td class="small-caption">' . sprintf( lang_get( 'label' ), $t_date_added ) . '</td>';

                                            echo '<td class="small-caption">';
                                            print_user( $t_sponsorship->user_id );
                                            echo '</td>';

                                            echo '<td class="small-caption">' . sponsorship_format_amount( $t_sponsorship->amount ) . '</td>';
                                            if( access_has_bug_level( config_get( 'handle_sponsored_bugs_threshold' ), $f_bug_id ) ) {
                                                echo '<td class="small-caption">' . get_enum_element( 'sponsorship', $t_sponsorship->paid ) . '</td>';
                                            }

                                            echo '</tr>';
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
            </div>
        </div>
    </div>
