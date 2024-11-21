(function ($cms) {
    /*global jQuery:false*/
    'use strict';

    // Assume that our activity feed needs updating to start with
    if (window.latestActivity === undefined) {
        window.latestActivity = 0;
        window.lastId = 0;
        window.sAjaxUpdateLocking = 0;
        window.activitiesFeedGrow = true;
    }

    /*
     This provides the JavaScript necessary for the "status" part of activities
     */
    $cms.views.BlockMainActivitiesState = BlockMainActivitiesState;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function BlockMainActivitiesState() {
        BlockMainActivitiesState.base(this, 'constructor', arguments);

        this.form = this.$('form.js-form-status-updates');
        this.submitBtn = this.$('.js-btn-submit-update');
        this.textarea = this.$('textarea.js-textarea-activity-status');
        this.notificationEl = this.$('.js-el-activities-update-notification');
    }

    $util.inherits(BlockMainActivitiesState, $cms.View, /**@lends BlockMainActivitiesState#*/{
        events: function () {
            return {
                'focus textarea.js-textarea-activity-status': 'textareaFocus',
                'blur textarea.js-textarea-activity-status': 'textareaBlur',
                'keyup textarea.js-textarea-activity-status': 'textareaKeyup',
                'keypress textarea.js-textarea-activity-status': 'textareaKeypress',

                'keyup .js-textarea-activity-status': 'manageScrollHeight',

                'submit form.js-form-status-updates': 'submitForm'
            };
        },

        textareaFocus: function (e, textarea) {
            if (textarea.value.trim() === '{!activity_feed:TYPE_HERE;^}') {
                textarea.value = '';
                textarea.classList.add('field-input-filled');
                textarea.classList.remove('field-input-non-filled');
            }
            textarea.classList.remove('fade-input');
        },
        textareaBlur: function (e, textarea) {
            if (textarea.value.trim() === '') {
                textarea.value = '{!activity_feed:TYPE_HERE;^}';
                textarea.classList.remove('field-input-filled');
                textarea.classList.add('field-input-non-filled');
            }

            textarea.classList.add('fade-input');
        },
        textareaKeyup: function (e, textarea) {
            if (!$cms.isMobile()) {
                $cms.ui.manageScrollHeight(textarea);
            }
            this.maintainCharCount();
        },
        textareaKeypress: function () {
            this.maintainCharCount();
        },

        /**
         * Called on update submission
         */
        submitForm: function (e, form) {
            var subjectText = this.textarea.value.trim();

            if (e) {
                e.preventDefault();
            }

            if ((subjectText === '') || (subjectText === '{!activity_feed:TYPE_HERE;^}')) {
                this.notificationEl.className = 'update_error';
                this.notificationEl.textContent = '{!activity_feed:PLEASE_ENTER_STATUS;^}';
            } else {
                var view = this;
                jQuery.ajax({
                    url: '{$FIND_SCRIPT_NOHTTP;,activity_feed_handler}' + $cms.keep(true),
                    type: 'POST',
                    data: $dom.serialize(form),
                    cache: false,
                    timeout: 5000,
                    // Processes data retrieved for the activities feed and updates the list
                    complete: function (jqXHR, textStatus) {
                        view.submitBtn.disabled = false;

                        var tokenField = form.elements['csrf_token'];
                        if (tokenField) {
                            $cms.getCsrfToken().then(function (text) {
                                $util.log('Regenerated CSRF token');

                                tokenField.value = text;
                            });
                        }

                        if ((textStatus !== 'success') || !jqXHR.responseXML) {
                            view.notificationEl.className = 'update_error';
                            view.notificationEl.textContent = '{!activity_feed:WENT_WRONG;^}';
                            $dom.hide(view.notificationEl);

                            $dom.fadeIn(view.notificationEl, 1200).then(function () {
                                setTimeout(function () {
                                    $dom.fadeOut(view.notificationEl, 1200).then(function () {
                                        view.maintainCharCount();
                                        $dom.fadeIn(view.notificationEl, 1200);
                                    });
                                }, 2400);
                            });
                            return;
                        }

                        var xmlDoc = jqXHR.responseXML,
                            successEl = xmlDoc.querySelector('success'),
                            feedbackEl = xmlDoc.querySelector('feedback');

                        if (successEl.textContent === '0') {
                            if (feedbackEl.textContent.startsWith('{!MUST_LOGIN;^}')) { //if refusal is due to login expiry...
                                $cms.ui.alert('{!MUST_LOGIN;^}');
                            } else {
                                view.notificationEl.className = 'update_error';
                                view.notificationEl.textContent = feedbackEl.textContent;
                            }
                        } else if (successEl.textContent === '1') {
                            view.notificationEl.className = 'update_success';
                            view.notificationEl.textContent = feedbackEl.textContent;

                            if ($dom.$('#activities-feed')) { // The update box won't necessarily have a displayed feed to update
                                sUpdateGetData();
                            }

                            $dom.fadeIn(view.notificationEl, 1200).then(function () {
                                $dom.fadeOut(view.notificationEl, 1200).then(function () {
                                    view.notificationEl.className = 'update_success';
                                    view.notificationEl.textContent = '254 {!activity_feed:CHARACTERS_LEFT;^}';
                                    $dom.fadeIn(view.notificationEl, 1200);

                                    var textareaParentEl = view.textarea.parentElement;

                                    $dom.height(textareaParentEl, $dom.height(textareaParentEl));

                                    view.textarea.value = '{!activity_feed:TYPE_HERE;^}';
                                    view.textarea.classList.remove('field-input-filled');
                                    view.textarea.classList.add('field-input-non-filled');

                                    $dom.fadeIn(view.textarea, 1200).then(function () {
                                        $dom.height(textareaParentEl, '');
                                    });
                                });
                            });
                        }
                    }
                });
            }
        },

        // Maintain feedback on how many characters are available in an update box.
        maintainCharCount: function () {
            var charCount = this.textarea.value.length;

            if (charCount < 255) {
                this.notificationEl.className = 'update_success';
                this.notificationEl.textContent = (254 - charCount) + ' {!activity_feed:CHARACTERS_LEFT;^}';
            } else {
                this.notificationEl.className = 'update_error';
                this.notificationEl.textContent = (charCount - 254) + ' {!activity_feed:CHARACTERS_TOO_MANY;^}';

            }
        }
    });

    $cms.templates.cnsMemberProfileActivities = function cnsMemberProfileActivities(params) {
        var syndications = params.syndications,
            syndication;

        for (var hook in syndications) {
            syndication = syndications[hook];

            if (syndication.syndicationJavascriptFunctionCalls != null) {
                $cms.executeJsFunctionCalls(syndication.syndicationJavascriptFunctionCalls);
            }
        }
    };

    $cms.templates.activity = function activity(params, container) {
        var liid = strVal(params.liid);

        $dom.on(container, 'click', '.js-confirm-update-remove', function (e) {
            sUpdateRemove(e, liid);
        });
    };

    $cms.templates.blockMainActivities = function blockMainActivities(params) {
        if (!params.isBlockRaw) {
            window.activitiesMode = strVal(params.mode);
            window.activitiesMemberIds = strVal(params.memberIds);

            if (params.start === 0) {
                // "Grow" means we should keep stacking new content on top of old. If not
                // then we should allow old content to "fall off" the bottom of the feed.
                window.activitiesFeedGrow = Boolean(params.grow);
                window.activitiesFeedMax = params.max;
                if (document.getElementById('activities-feed')) {
                    setInterval(sUpdateGetData, params.refreshTime * 1000);
                }
            }
        }
    };

    function sUpdateGetData() {
        // Lock feed updates by setting sAjaxUpdateLocking to 1
        if ((++window.sAjaxUpdateLocking) > 1) {
            window.sAjaxUpdateLocking = 1;
        } else {
            // First we check whether our feed is already up to date
            jQuery.ajax({
                url: $util.rel('data_custom/latest_activity.bin?cache_break=' + Math.floor(Math.random() * 10000)),
                data: {},
                success: function (data) {
                    if (parseInt(data) !== window.latestActivity) {
                        // If not then remember the new value
                        window.latestActivity = parseInt(data);

                        // Now grab whatever updates are available (this is NOT pagination, it's checking for updates and doesn't use the block code)
                        var url = '{$FIND_SCRIPT_NOHTTP;,activity_feed_updater}?last_id=' + window.lastId + '&mode=' + window.activitiesMode + '&max=' + window.activitiesFeedMax + $cms.keep(),
                            listElements = jQuery('li', '#activities-feed');
                        window.lastId = ((listElements.attr('id') == null) ? '-1' : listElements.attr('id').replace(/^activity-/, ''));

                        if ((window.activitiesMemberIds != null) && (window.activitiesMemberIds !== '')) {
                            url += '&member_ids=' + window.activitiesMemberIds;
                        }

                        jQuery.ajax({
                            url: url,
                            cache: false,
                            timeout: 5000,
                            success: function (data, stat) {
                                sUpdateShow(data, stat);
                            },
                            error: function (a, stat, err) {
                                sUpdateShow(err, stat);
                            }
                        });
                    } else {
                        // Allow feed updates
                        window.sAjaxUpdateLocking = 0;
                    }
                },
                dataType: 'text'
            });
        }
    }

    /**
     * Receive and parse data for the activities activities feed
     */
    function sUpdateShow(data, stat) {
        if (window.sAjaxUpdateLocking > 1) {
            window.sAjaxUpdateLocking = 1;
        } else {
            var succeeded = false;
            if (stat === 'success') {
                if (jQuery('success', data).text() === '1') {
                    var listElements = jQuery('li', '#activities-feed'); // Querying from current browser DOM
                    var listItems = jQuery('listitem', data); // Querying from XML definition o new data

                    listElements.removeAttr('toFade');

                    // Add in new items
                    var topOfList = document.getElementById('activities-holder').firstChild;
                    jQuery.each(listItems, function () {
                        var thisLi = document.createElement('li');
                        thisLi.id = 'activity-' + jQuery(this).attr('id');
                        window.lastId = jQuery(this).attr('id');
                        thisLi.className = 'activities-box box';
                        thisLi.setAttribute('toFade', 'yes');
                        topOfList.parentNode.insertBefore(thisLi, topOfList);
                        $dom.html(thisLi, window.atob(jQuery(this).text()));
                    });

                    var noMessages = document.getElementById('activity_-1');
                    if (noMessages) {
                        noMessages.style.display = 'none';
                    }

                    listElements = jQuery('li', '#activities-feed'); // Refresh, so as to include the new activity nodes

                    if ((!window.activitiesFeedGrow) && (listElements.length > window.activitiesFeedMax)) {// Remove anything passed the grow length
                        for (var i = window.activitiesFeedMax; i < listElements.length; i++) {
                            listElements.last().remove();
                        }
                    }

                    jQuery('#activities-general-notify').text('');
                    jQuery('li[toFade="yes"]', '#activities-feed').hide().fadeIn(1200);
                    succeeded = true;
                } else {
                    if (jQuery('success', data).text() === '2') {
                        jQuery('#activities-general-notify').text('');
                        succeeded = true;
                    }
                }
            }
            if (!succeeded) {
                jQuery('#activities-general-notify').text('{!INTERNAL_ERROR;^,519eab7583204dddb599b9be46e2c9ed}');
            }
            window.sAjaxUpdateLocking = 0;
        }
    }

    function sUpdateRemove(event, id) {
        $cms.ui.confirm(
            '{!activity_feed:DELETE_CONFIRM;^}',
            function (result) {
                if (result) {
                    var url = '{$FIND_SCRIPT_NOHTTP;,activity_feed_removal}' + $cms.keep(true);

                    $cms.getCsrfToken().then(function (text) {
                        $util.log('Regenerated CSRF token');

                        var postVal = 'removal_id=' + id;
                        postVal += '&csrf_token=' + encodeURIComponent(text);

                        jQuery.ajax({
                            url: url,
                            type: 'POST',
                            data: postVal,
                            cache: false,
                            timeout: 5000,
                            success: function (data, stat) {
                                sUpdateRemoveShow(data, stat);
                            },
                            error: function (a, stat, err) {
                                sUpdateRemoveShow(err, stat);
                            }
                        });
                    });
                }
            }
        );
        event.preventDefault();
    }

    function sUpdateRemoveShow(data, stat) {
        var statusId = '';

        var animationSpeed = 1600;

        if (stat === 'success') {
            if (jQuery('success', data).text() === '1') {
                statusId = '#activity-' + jQuery('status_id', data).text();
                jQuery('.activities-content', statusId, '#activities-feed').text(jQuery('feedback', data).text()).addClass('activities-content--remove-success').hide().fadeIn(animationSpeed, function () {
                    jQuery(statusId, '#activities-feed').fadeOut(animationSpeed, function () {
                        jQuery(statusId, '#activities-feed').remove();
                    });
                });
            } else {
                switch (jQuery('err', data).text()) {
                    case 'perms':
                        statusId = '#activity-' + jQuery('status_id', data).text();
                        var backupUpText = jQuery('.activities-content', statusId, '#activities-feed').text();
                        jQuery('.activities-content', statusId, '#activities-feed').text(jQuery('feedback', data).text()).addClass('activities-content--remove-failure').hide().fadeIn(animationSpeed, function () {
                            jQuery('.activities-content', statusId, '#activities-feed').fadeOut(animationSpeed, function () {
                                jQuery('.activities-content', statusId, '#activities-feed').text(backupUpText).removeClass('activities-content--remove-failure').fadeIn(animationSpeed);
                            });
                        });
                        break;
                    case 'missing':
                    default:
                        break;
                }
            }
        }
    }
}(window.$cms));
