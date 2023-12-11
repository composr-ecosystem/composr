<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Give the specified workflow a new approval point.
 * Note: The workflow must already exist with at least one approval point.
 * See the 'build_new_workflow' function to define a new workflow.
 * Note: If passing a approval point already in the given workflow, its position will be updated to the new value (or the end if not specified).
 * NOTE: Positions do not need to be contiguous, as long as they are in an order. For example, approval points at positions 12, 34 and 852 will
 * behave the same way as approval points pointnts at positions 1, 2 and 3 (except when adding approval points at defined positions of course, since
 * position 4 will appear at the start of the former list but at the end of the latter)
 *
 * @param  array $bits Language string bits
 * @param  AUTO_LINK $workflow_id The ID of the workflow to add this approval point to
 * @param  ?integer $position The position in the workflow that this approval point will have. null adds it to the end (null: default)
 * @return AUTO_LINK Requirement ID
 */
function add_approval_point_to_workflow($bits, $workflow_id, $position = null)
{
    // Now see what position we're adding to. We either need to determine the next position (if we've been given null), or else just dump
    // the position in the record (we don't assume that positions are unique in any case, points with the same position will appear
    // together but their specific order is undefined)
    if (is_null($position)) {
        // The easy case, we simply grab the existing approval points in order of position and +1 to the highest.
        $current_position = $GLOBALS['SITE_DB']->query_select_value('workflow_approval_points', 'MAX(the_position)', array('workflow_id' => $workflow_id));
        $position = is_null($current_position) ? 1 : ($current_position + 1);
    }

    // Do the insertion.
    $map = array(
       'workflow_id' => $workflow_id,
       'the_position' => $position,
    ) + $bits;
    return $GLOBALS['SITE_DB']->query_insert('workflow_approval_points', $map, true);
}

/**
 * Deleting a workflow will remove the workflow, leaving the validated/unvalidated system to handle content, ie. content which has passed
 * completely through the workflow will have its validated bit set and will thus remain live. Those not completely through will not have
 * theirs set yet and will thus remain unvalidated and not live.
 * NOTE: Approval points can be reused, so they will stay behind.
 *
 * @param  AUTO_LINK $id The ID of the workflow to delete
 */
function delete_workflow($id)
{
    // Grab all of the content in this workflow
    $content = $GLOBALS['SITE_DB']->query_select('workflow_content', array('id', 'content_type', 'content_id'), array('workflow_id' => $id));

    // Now remove those references
    $GLOBALS['SITE_DB']->query_delete('workflow_content', array('workflow_id' => $id));

    // Then remove their workflow status
    foreach ($content as $content_item) {
        $GLOBALS['SITE_DB']->query_delete('workflow_content_status', array('workflow_content_id' => $content_item['id']));
    }

    // Grab the approval points in this workflow and remove those which aren't used by any other workflows
    $points = $GLOBALS['SITE_DB']->query_select('workflow_approval_points', array('id', 'workflow_approval_name'), array('workflow_id' => $id));
    foreach ($points as $p) {
        delete_lang($p['workflow_approval_name']);
        $GLOBALS['SITE_DB']->query_delete('workflow_permissions', array('workflow_approval_point_id' => $p['id']));
    }

    // Now remove the workflow from the database and remove its association with approval points
    delete_lang($GLOBALS['SITE_DB']->query_select_value('workflows', 'workflow_name', array('id' => $id)));
    $GLOBALS['SITE_DB']->query_delete('workflows', array('id' => $id), '', 1);
    $GLOBALS['SITE_DB']->query_delete('workflow_approval_points', array('workflow_id' => $id));
}

/**
 * Deleting an approval point will remove it from any workflow it is a part of. Any content which has been approved for this approval point will be unaffected, while those
 * not-yet-approved will first be approved, then have the approval point removed. This is to prevent any content asking to be approved on a point which doesn't exist.
 *
 * @param  AUTO_LINK $name The workflow approval point ID
 */
function delete_approval_point($name)
{
    // Grab all content awaiting this approval
    $content = $GLOBALS['SITE_DB']->query_select('workflow_content_status', array('status_code', 'workflow_content_id'), array('workflow_approval_point_id' => $name));

    // Now go through each, approving them if needed
    foreach ($content as $content_item) {
        // 0 means unapproved
        if ($content['status_code'] == 0) {
            // Set the approval
            approve_content_for_point($content['workflow_content_id'], $name);
        }
    }
    // Now remove these approval points en-mass from the content
    $GLOBALS['SITE_DB']->query_delete('workflow_content_status', array('workflow_content_id' => $name));

    // We have to be careful about removing approval points from workflows, since a workflow is defined by the approval points it requires.
    // We must check to see if we're about to throw out any workflows as  result of removing this point. If so then we'd like to remove the workflow sanely and completely.
    $affected_workflows = $GLOBALS['SITE_DB']->query_select('workflow_approval_points', array('workflow_id'), array('workflow_approval_name' => $name));
    foreach ($affected_workflows as $workflow) {
        // If there is only one approval point then it's the one we've been asked to remove. Let's just remove the whole workflow.
        if (count(get_approval_points_for_workflow($workflow['workflow_id'])) == 1) {
            delete_workflow($workflow['workflow_id']); // Otherwise we can just remove this one point
        } else {
            $GLOBALS['SITE_DB']->query_delete('workflow_approval_points', array('workflow_approval_name' => $name, 'workflow_id' => $workflow['workflow_id']), '', 1);
        }
    }

    // Now we remove the permissions associated with this approval point
    $GLOBALS['SITE_DB']->query_delete('workflow_permissions', array('workflow_approval_point_id' => $name));
}
