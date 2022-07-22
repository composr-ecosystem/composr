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
                    $dom.submit(select.form);
                } else {
                    $cms.ui.alert('{!NOTHING_SELECTED;}');
                }
            }
        },

        maxChangeSubmitForm: function (e, select) {
            $dom.submit(select.form);
        },

        orderChangeSubmitForm: function (e, select) {
            $dom.submit(select.form);
        },
    });

    $cms.functions.moduleTopicsPostJavascript = function moduleTopicsPostJavascript(size, stub) {
        stub = strVal(stub);

        var form = document.getElementById('post').form;
        form.addEventListener('submit', function (submitEvent) {
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
                $dom.cancelSubmit(submitEvent);
                $cms.ui.alert('{!cns:POST_TOO_LONG;}');
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
        });
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
        var existing = document.getElementById('existing'),
            form = existing.form;

        form.addEventListener('change', pollFormElementsChangeListener);

        function pollFormElementsChangeListener() {
            var disableAll = (existing.selectedIndex !== 0);
            for (var i = 0; i < form.elements.length; i++) {
                if ((form.elements[i] !== existing) && (form.elements[i].id !== 'perform_keywordcheck') && ((form.elements[i].type === 'checkbox') || (form.elements[i].type === 'text'))) {
                    $cms.form.setRequired(form.elements[i].name, (!disableAll) && ((form.elements[i].id === 'question') || (form.elements[i].id === 'answer_0')));
                    $cms.form.setLocked(form.elements[i], disableAll);
                }
            }
        }
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

    var newTopicFormOrigAction;
    $cms.functions.newTopicFormChangeActionIfAddingPoll = function newTopicFormChangeActionIfAddingPoll(options) {
        var addPollCheckbox = (document.getElementsByName('add_poll') || [])[0],
            addPollUrl = strVal(options.addPollUrl);

        if (!addPollCheckbox) {
            return;
        }

        if (newTopicFormOrigAction == null) {
            newTopicFormOrigAction = addPollCheckbox.form.action;
        }

        addPollCheckbox.form.action = (addPollCheckbox.checked) ? addPollUrl : newTopicFormOrigAction;

        addPollCheckbox.addEventListener('change', function () {
            addPollCheckbox.form.action = (addPollCheckbox.checked) ? addPollUrl : newTopicFormOrigAction;
        })
    };

    $cms.templates.cnsVirtualForumFiltering = function cnsVirtualForumFiltering() {
        var container = this;

        $dom.on(container, 'change', '.js-select-change-form-submit', function (e, select) {
            $dom.submit(select.form);
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
        var markedPostActionsForm = container.querySelector('form.js-form-marked-post-actions');

        if ((params.serializedOptions !== undefined) && (params.hash !== undefined)) {
            window.commentsSerializedOptions = params.serializedOptions;
            window.commentsHash = params.hash;
        }

        $dom.on(container, 'click', '.js-click-check-marked-form-and-submit', function (e, clicked) {
            if (!$cms.form.addFormMarkedPosts(markedPostActionsForm, 'mark_')) {
                $cms.ui.alert('{!NOTHING_SELECTED;}');
                e.preventDefault();
                return;
            }

            if (document.getElementById('mpa-type').selectedIndex === -1) {
                e.preventDefault();
                return;
            }

            $cms.ui.disableButton(clicked);
        });

        $dom.on(container, 'change', '.js-topic-moderator-action-submit-form', function (e, select) {
            if (select.selectedIndex !== -1) {
                $dom.submit(select.form);
            }
        });

        $dom.on(container, 'change', '.js-moderator-action-submit-form', function (e, select) {
            if (select.selectedIndex !== -1) {
                if ($cms.form.addFormMarkedPosts(select.form, 'mark_')) {
                    $dom.submit(select.form);
                } else {
                    $cms.ui.alert('{!NOTHING_SELECTED;}');
                }
            }
        });

        $dom.on(container, 'change', '.js-order-change-submit-form', function (e, select) {
            $dom.submit(select.form);
        });
    };

    $cms.templates.cnsTopicPoll = function (params) {
        var form = this,
            minSelections = Number(params.minimumSelections) || 0,
            maxSelections = Number(params.maximumSelections) || 0,
            error = (minSelections === maxSelections) ? $util.format('{!cns:POLL_NOT_ENOUGH_ERROR_2;^}', minSelections) : $util.format('{!cns:POLL_NOT_ENOUGH_ERROR;^}', [minSelections, maxSelections]);

        $dom.on(form, 'submit', function (e) {
            if (cnsCheckPoll() === false) {
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
        }
    };

    $cms.templates.cnsNotification = function (params) {
        var container = this,
            ignoreUrl = params.ignoreUrl2;

        $dom.on(container, 'click', '.js-click-ignore-notification', function () {
            var el = this;
            $cms.doAjaxRequest(ignoreUrl, function () {
                var o = el.parentNode.parentNode.parentNode.parentNode;
                o.parentNode.removeChild(o);

                var nots = document.querySelector('.cns-member-column-pts');
                if (nots && !document.querySelector('.cns-notification')) {
                    nots.parentNode.removeChild(nots);
                }
            });
        });
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
}(window.$cms, window.$util, window.$dom));
