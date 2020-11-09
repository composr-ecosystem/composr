(function ($cms) {
    'use strict';

    $cms.templates.blockTwitterFeed = function blockTwitterFeed() {
        (function (d, s, id) {
            var js, fjs = d.querySelector(s);

            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = '//platform.twitter.com/widgets.js';
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, 'script', 'twitter-wjs'));
    };

    $cms.templates.blockTwitterFeedStyle = function blockTwitterFeedStyle(params) {
        $cms.createRollover(params.replyId, $util.srl('{$IMG;,twitter_feed/reply_hover}'));
        $cms.createRollover(params.retweetId, $util.srl('{$IMG;,twitter_feed/retweet_hover}'));
        $cms.createRollover(params.favoriteId, $util.srl('{$IMG;,twitter_feed/favorite_hover}'));
    };
}(window.$cms));
