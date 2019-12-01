<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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

    define('RATING_TYPE_star_choice', 0);
    define('RATING_TYPE_like_dislike', 1);

    global $RATINGS_STRUCTURE;
    $RATINGS_STRUCTURE = [
        'catalogues__links' => [
            RATING_TYPE_like_dislike,
            [
                '' => '',
            ],
        ],
        'images' => [
            RATING_TYPE_star_choice,
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
