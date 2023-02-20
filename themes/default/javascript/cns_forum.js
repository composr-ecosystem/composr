(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.CnsForumTopicWrapper = CnsForumTopicWrapper;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function CnsForumTopicWrapper() {
        CnsForumTopicWrapper.base(this, 'constructor', arguments);
    }

    $util.inherits(CnsForumTopicWrapper, $cms.View, /**@lends CnsForumTopicWrapper#*/{
        events: function () {
            return {
                'click .js-click-mark-all-topics': 'markAllTopics',
                'change .js-moderator-action-submit-form': 'moderatorActionSubmitForm',
                'change .js-max-change-submit-form': 'maxChangeSubmitForm',
                'change .js-order-change-submit-form': 'orderChangeSubmitForm',
            };
        },

        markAllTopics: function () {
            $dom.$$('input[type="checkbox"][name^="mark_"]').forEach(function (checkbox) {
                checkbox.click();
            });
        },

        moderatorActionSubmitForm: function (e, select) {
            if (select.selectedIndex !== 0) {
                if ($cms.form.addFormMarkedPosts(select.form, 'mark_')) {
                    select.form.submit();
                } else {
                    $cms.ui.alert('{!NOTHING_SELECTED;}');
                }
            }
        },

        maxChangeSubmitForm: function (e, select) {
            select.form.submit();
        },

        orderChangeSubmitForm: function (e, select) {
            select.form.submit();
        },
    });

    $cms.functions.moduleTopicsPostJavascript = function moduleTopicsPostJavascript(size, stub) {
        stub = strVal(stub);

        var usernameField = document.getElementById('to_member_id_0');
        if (usernameField) {
            var checkPtUsername = function (event) {
                var usernameField = event.target;
                if (usernameField.value.trim() !== '') {
                    $cms.loadSnippet('pt_rules&username=' + usernameField.value.trim()).then(function (result) {
                        if (result === '-1') {
                            // Missing member
                            $cms.ui.alert('{!MEMBER_NO_EXIST;^}');
                        } else if (result === '-2') {
                            // Permission denied
                            $cms.ui.alert('{!cns:NO_PT_FROM_ALLOW;^}');
                        } else if (result !== '') {
                            // Rules
                            $cms.ui.confirm('{!cns:PT_RULES_PAGE_INTRO;^,xxx}'.replace(/xxx/, usernameField.value.trim()) + '<br /><br />' + result, function (result2) {
                                if (!result2) {
                                    usernameField.value = '';
                                }
                            }, '{!RULES;^}: {!I_AGREE;^}', true);
                        }
                    });
                }
            };
            usernameField.onchange = checkPtUsername; // We use onchange because that can be replicated via ensureNextField
        }

        var extraChecks = [];
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var post = form.elements['post'],
                textValue;

            if ($cms.form.isWysiwygField(post)) {
                try {
                    textValue = window.CKEDITOR.instances['post'].getData();
                } catch (ignore) {
                    // continue
                }
            } else {
                if (!post.value && post[1]) {
                    post = post[1];
                }
                textValue = post.value;
            }

            if (textValue.length > size) {
                $cms.ui.alert('{!cns:POST_TOO_LONG;}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = textValue;
                return false;
            }

            if (stub !== '') {
                var df = stub;
                var pv = post.value;
                if (post && (pv.substring(0, df.length) === df)) {
                    pv = pv.substring(df.length, pv.length);
                }
                post.value = pv;
            }

            return true;
        });
        return extraChecks;
    };

    $cms.functions.moduleTopicsPostJavascriptForceGuestNames = function moduleTopicsPostJavascriptForceGuestNames() {
        var posterNameIfGuest = document.querySelector('input[name="name"]');
        if (posterNameIfGuest) {
            var crf = function () {
                if (posterNameIfGuest.value === '{!GUEST;}') {
                    posterNameIfGuest.value = '';
                }
            };
            crf();
            posterNameIfGuest.addEventListener('change', crf);
            posterNameIfGuest.addEventListener('blur', crf);
        }
    };

    $cms.functions.moduleTopicsAddPoll = function moduleTopicsAddPoll() {
        // Adding an existing poll
        var existing = document.getElementById('existing');
        var form;

        if (existing) {
            form = existing.form;
            existing.addEventListener('change', pollFormElementsChangeListener);
        }

        function pollFormElementsChangeListener() {
            var copyingExistingPoll = existing.selectedIndex !== 0; // If copying from an existing poll, we disable all the poll related fields
            for (var i = 0; i < form.elements.length; i++) {
                var fieldName = form.elements[i].name;
                var isPollField = ['question', 'is_open', 'requires_reply', 'may_unblind_own_poll', 'is_private', 'minimum_selections', 'maximum_selections', 'closing_time'].includes(fieldName) || form.elements[i].name.substr(0, 7) === 'answer_';
                var isRequiredPollField = ['question', 'answer_0'].includes(fieldName);
                if (isPollField) {
                    $cms.form.setRequired(form.elements[i].name, !copyingExistingPoll && isRequiredPollField);
                    if (form.elements[i].type === 'checkbox') {
                        form.elements[i].disabled = copyingExistingPoll;
                    } else {
                        form.elements[i].readOnly = copyingExistingPoll;
                    }
                }
            }
        }

        // Adding / editing a poll
        var extraChecks = [];
        extraChecks.push(function (e, form2, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var error;

            var confinedElement = form2.elements['answers-confined'];
            var confined; // array
            if (confinedElement) {
                confined = JSON.parse(confinedElement.value);
            }

            var entries = [];
            for (var i = 0; i < form2.elements.length; i++) {
                if (!form2.elements[i].name.startsWith('answer_')) {
                    continue;
                }
                if (form2.elements[i].value !== '') {
                    // For confined polls, if a disallowed option is provided, error
                    if (confined !== undefined && confined.indexOf(form2.elements[i].value) === -1) {
                        error = $util.format('{!cns_polls:POLL_INVALID_OPTION;^}', [form2.elements[i].value]);
                        $cms.ui.alert(error);
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form2.elements[i];
                        return false;
                    }

                    // Disallow duplicate options
                    if (entries.indexOf(form2.elements[i].value) !== -1) {
                        error = $util.format('{!cns_polls:POLL_NO_DUPLICATE_OPTIONS;^}', [form2.elements[i].value]);
                        $cms.ui.alert(error);
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form2.elements[i];
                        return false;
                    }

                    entries.push(form2.elements[i].value);
                }
            }

            return true;
        });
        return extraChecks;
    };

    $cms.functions.moduleAdminCnsForums = function moduleAdminCnsForums() {
        if (document.getElementById('delete')) {
            var form = document.getElementById('delete').form;
            var crf = function () {
                form.elements['target_forum'].disabled = (!form.elements['delete'].checked);
                form.elements['delete_topics'].disabled = (!form.elements['delete'].checked);
            };
            crf();
            form.elements['delete'].addEventListener('change', crf);
        }
    };

    $cms.functions.moduleAdminCnsForumGroupings = function moduleAdminCnsForumGroupings() {
        if (document.getElementById('delete')) {
            var form = document.getElementById('delete').form;
            var crf = function () {
                form.elements['target_forum_grouping'].disabled = (!form.elements['delete'].checked);
            };
            crf();
            form.elements['delete'].addEventListener('change', crf);
        }
    };

    var newTopicFormOrigAction = null;
    $cms.functions.newTopicFormChangeActionIfAddingPoll = function newTopicFormChangeActionIfAddingPoll(options) {
        var addPollCheckbox = (document.getElementsByName('add_poll') || [])[0],
            addPollUrl = strVal(options.addPollUrl);

        if (!addPollCheckbox) {
            return;
        }

        if (newTopicFormOrigAction === null) {
            newTopicFormOrigAction = addPollCheckbox.form.action;
        }

        if (addPollCheckbox.checked) {
            addPollCheckbox.form.elements['csrf_token_preserve'].value = '1';
            addPollCheckbox.form.action = addPollUrl;
        } else {
            addPollCheckbox.form.elements['csrf_token_preserve'].value = '0';
            addPollCheckbox.form.action = newTopicFormOrigAction;
        }

        addPollCheckbox.addEventListener('change', function () {
            if (addPollCheckbox.checked) {
                addPollCheckbox.form.elements['csrf_token_preserve'].value = '1';
                addPollCheckbox.form.action = addPollUrl;
            } else {
                addPollCheckbox.form.elements['csrf_token_preserve'].value = '0';
                addPollCheckbox.form.action = newTopicFormOrigAction;
            }
        });
    };

    $cms.templates.cnsVirtualForumFiltering = function cnsVirtualForumFiltering() {
        var container = this;

        $dom.on(container, 'change', '.js-select-change-form-submit', function (e, select) {
            select.form.submit();
        });
    };

    $cms.templates.cnsForumInGrouping = function cnsForumInGrouping(params, container) {
        var forumRulesUrl = params.forumRulesUrl,
            introQuestionUrl = params.introQuestionUrl;

        $dom.on(container, 'click', '.js-click-open-forum-rules-popup', function () {
            $cms.ui.open($util.rel($cms.maintainThemeInLink(forumRulesUrl)), '', 'width=600,height=auto,status=yes,resizable=yes,scrollbars=yes');
        });

        $dom.on(container, 'click', '.js-click-open-intro-question-popup', function () {
            $cms.ui.open($util.rel($cms.maintainThemeInLink(introQuestionUrl)), '', 'width=600,height=auto,status=yes,resizable=yes,scrollbars=yes');
        });
    };

    $cms.templates.cnsTopicScreen = function (params, /**Element*/container) {
        if ((params.serializedOptions !== undefined) && (params.hash !== undefined)) {
            window.commentsSerializedOptions = params.serializedOptions;
            window.commentsHash = params.hash;
        }

        $dom.on(container, 'change', '.js-topic-moderator-action-submit-form', function (e, select) {
            if (select.selectedIndex !== -1) {
                select.form.submit();
            }
        });

        $dom.on(container, 'change', '.js-moderator-action-submit-form', function (e, select) {
            if (select.selectedIndex !== -1) {
                if ($cms.form.addFormMarkedPosts(select.form, 'mark_')) {
                    select.form.submit();
                } else {
                    $cms.ui.alert('{!NOTHING_SELECTED;}');
                }
            }
        });

        $dom.on(container, 'change', '.js-order-change-submit-form', function (e, select) {
            select.form.submit();
        });
    };

    $cms.templates.cnsTopicPoll = function (params, container) {
        var form = this,
            minSelections = Number(params.minimumSelections) || 0,
            maxSelections = Number(params.maximumSelections) || 0,
            error = (minSelections === maxSelections) ? $util.format('{!cns_polls:POLL_INVALID_SELECTION_COUNT_2;^}', [minSelections]) : $util.format('{!cns_polls:POLL_INVALID_SELECTION_COUNT;^}', [minSelections, maxSelections]);

        $dom.on(container, 'click', '.js-view-poll', function (e, btn) {
            form.action = strVal(btn.dataset.formAction);
        });

        $dom.on(container, 'click', '.js-revoke-poll', function (e, btn) {
            form.action = strVal(btn.dataset.formAction);
        });

        $dom.on(container, 'click', '.js-vote-poll', function (e, btn) {
            form.action = strVal(btn.dataset.formAction);

            if (!cnsCheckPoll()) {
                e.preventDefault();
            }
        });

        function cnsCheckPoll() {
            var j = 0;
            for (var i = 0; i < form.elements.length; i++) {
                if (form.elements[i].checked && ((form.elements[i].type === 'checkbox') || (form.elements[i].type === 'radio'))) {
                    j++;
                }
            }
            var answer = ((j >= minSelections) && (j <= maxSelections));
            if (!answer) {
                $cms.ui.alert(error);
                return false;
            }

            $cms.ui.disableButton(form.elements['poll-vote-button']);

            return true;
        }
    };

    $cms.templates.cnsTopicPost = function cnsTopicPost(params, container) {
        var id = strVal(params.id),
            cell = $dom.$('#cell-mark-' + id);


        $dom.on(container, 'click', '.js-click-checkbox-set-cell-mark-class', function (e, checkbox) {
            cell.classList.toggle('cns-on', checkbox.checked);
            cell.classList.toggle('cns-off', !checkbox.checked);
        });
    };

    $cms.templates.cnsTopicMarker = function cnsTopicMarker(params, container) {
        $dom.on(container, 'click', '.js-click-checkbox-set-row-mark-class', function (e, checkbox) {
            var row = $dom.closest(checkbox, 'tr');
            row.classList.toggle('cns-on', checkbox.checked);
            row.classList.toggle('cns-off', !checkbox.checked);
        });
    };

    $cms.functions.topicDeleteScreen = function topicDeleteScreen() {
        $dom.on('#select_topic_id', 'change', function (e, el) {
            el.form.elements['reverse_point_transaction'].disabled = (el.value !== '');
        });

        $dom.on('#manual_topic_id', 'change', function (e, el) {
            el.form.elements['reverse_point_transaction'].disabled = (el.value !== '');
        });
    };

    /**
     * Prepare the UI to reply to a post in a topic
     * @param isThreaded
     * @param id
     * @param replyingToUsername
     * @param replyingToPost
     * @param replyingToPostPlain
     * @param isExplicitQuote
     */
    $cms.functions.topicReply = function topicReply(isThreaded, id, replyingToUsername, replyingToPost, replyingToPostPlain, isExplicitQuote) {
        isThreaded = Boolean(isThreaded);
        isExplicitQuote = Boolean(isExplicitQuote);

        var el = this,
            form = $dom.$('form#comments-form');

        var parentIdField;
        if (form.elements['parent_id'] === undefined) {
            parentIdField = document.createElement('input');
            parentIdField.type = 'hidden';
            parentIdField.name = 'parent_id';
            form.appendChild(parentIdField);
        } else {
            parentIdField = form.elements['parent_id'];
            if (window.lastReplyTo !== undefined) {
                window.lastReplyTo.style.opacity = 1;
            }
        }
        window.lastReplyTo = el;
        parentIdField.value = isThreaded ? id : '';

        el.classList.add('activated-quote-button');

        var post = form.elements['post'];

        $dom.smoothScroll($dom.findPosY(form, true));

        var outer = $dom.$('#comments-posting-form-outer');
        if (outer && !$dom.isDisplayed(outer)) {
            $cms.ui.toggleableTray(outer);
        }

        if (isThreaded) {
            post.value = $util.format('{!QUOTED_REPLY_MESSAGE;^}', [replyingToUsername, replyingToPostPlain]);
            post.stripOnFocus = post.value;
            post.classList.add('field-input-non-filled');
        } else {
            if ((post.stripOnFocus !== undefined) && (post.value === post.stripOnFocus)) {
                post.value = '';
            } else if (post.value !== '') {
                post.value += '\n\n';
            }

            post.focus();
            post.value += '[quote="' + replyingToUsername + '"]\n' + replyingToPost + '\n[snapback]' + id + '[/snapback][/quote]\n\n';

            if (!isExplicitQuote) {
                post.defaultSubstringToStrip = post.value;
            }
        }

        $cms.ui.manageScrollHeight(post);
        post.scrollTop = post.scrollHeight;
    };
}(window.$cms, window.$util, window.$dom));
