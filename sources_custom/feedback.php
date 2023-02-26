<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    custom_ratings
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__feedback()
{
    if (!addon_installed('custom_ratings')) {
        return;
    }

    define('MAX_LIKES_TO_SHOW', 20);

    define('RATING_TYPE__STAR_CHOICE', 0);
    define('RATING_TYPE__LIKE_DISLIKE', 1);

    global $RATINGS_STRUCTURE;
    $RATINGS_STRUCTURE = [
        'catalogues__links' => [
            RATING_TYPE__LIKE_DISLIKE,
            [
                '' => '',
            ],
        ],
        'images' => [
            RATING_TYPE__STAR_CHOICE,
            [
                '' => 'General',
                'scenery' => 'Scenery',
                'quality' => 'Quality',
                'art' => 'Artiness',
            ],
        ],
    ];

    global $REVIEWS_STRUCTURE;
    $REVIEWS_STRUCTURE = [
        'news' => [
            'Informative',
            'Insightful',
        ],
    ];
}
