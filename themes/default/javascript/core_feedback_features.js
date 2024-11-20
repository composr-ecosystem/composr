(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.CommentsPostingForm = CommentsPostingForm;
    /**
     * @memberof $cms.views
     * @class $cms.views.CommentsPostingForm
     * @extends $cms.View
     */
    function CommentsPostingForm(params) {
        CommentsPostingForm.base(this, 'constructor', arguments);

        this.btnSubmit = this.$('.js-btn-save-comment');
        this.form = this.btnSubmit.form;

        this.form.submitAction = this.form.action;
        this.form.submitTarget = this.form.target;

        $cms.requireJavascript(['jquery', 'jquery_autocomplete']).then(function () {
            window.$jqueryAutocomplete.setUpComcodeAutocomplete('post', Boolean(params.wysiwyg));
        });

        if ($cms.configOption('enable_previews') && $cms.isForcePreviews()) {
            this.btnSubmit.style.display = 'none';
        }

        this.addCommentChecking(this.form, this.params);
        if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '')) {
            this.addCaptchaChecking(this.form);
        }

        if (params.type && params.id) {
            this.initReviewRatings();
        }

        var captchaSpot = this.$('#captcha-spot');
        if (captchaSpot) {
            $dom.html(captchaSpot, params.captcha);
        }
    }

    $util.inherits(CommentsPostingForm, $cms.View, /**@lends $cms.views.CommentsPostingForm#*/{
        events: function () {
            return {
                'click .js-btn-full-editor': 'moveToFullEditor',
                'click .js-btn-save-comment': 'ensureRegularEditor',
                'click .js-click-do-form-preview': 'doPostingFormPreview',

                'click .js-img-review-bar': 'reviewBarClick',
                'mouseover .js-img-review-bar': 'reviewBarHover',
                'mouseout .js-img-review-bar': 'reviewBarHover',

                'focus .js-focus-textarea-post': 'focusTextareaPost',

                'click .js-click-open-site-emoticon-chooser-window': 'openEmoticonChooserWindow',

                'click .js-click-pd-on-mobile': 'preventDefaultOnMobile',
            };
        },

        initReviewRatings: function () {
            var self = this;

            this.$$('.js-container-review-rating').forEach(function (container) {
                self.displayReviewRating(container);
            });
        },

        displayReviewRating: function (container, rating) {
            var reviewBars = $dom.$$(container, '.js-img-review-bar');
            if (rating === undefined) {
                rating = $dom.$(container, '.js-inp-review-rating').value;
            }
            rating = Number(rating) || 0;

            reviewBars.forEach(function (reviewBar) {
                var barRating = Number(reviewBar.dataset.vwRating) || 0,
                    shouldHighlight = barRating <= rating; // Whether to highlight this bar

                reviewBar.classList.toggle('rating-star-highlight', shouldHighlight);
                reviewBar.classList.toggle('rating-star', !shouldHighlight);
            });
        },

        reviewBarClick: function (e, reviewBar) {
            var container = this.$closest(reviewBar, '.js-container-review-rating'),
                ratingInput = container.querySelector('.js-inp-review-rating');

            ratingInput.value = Number(reviewBar.dataset.vwRating) || 0;
            this.displayReviewRating(container);
        },

        reviewBarHover: function (e, reviewBar) {
            var container = this.$closest(reviewBar, '.js-container-review-rating'),
                rating = (e.type === 'mouseover') ? reviewBar.dataset.vwRating : undefined;

            this.displayReviewRating(container, rating);
        },

        focusTextareaPost: function (e, textarea) {
            var valueWithoutSpaces = textarea.value.replace(/\s/g, '');
            var placeholderTextOne = '{!POST_WARNING;^}'.replace(/\s/g, '');
            var placeholderTextTwo = '{!THREADED_REPLY_NOTICE;^,{!POST_WARNING}}'.replace(/\s/g, '');
            var placeholderTextThree = '{!THREADED_REPLY_NOTICE;^, }'.replace(/\s/g, '');

            if (
                (placeholderTextOne && (valueWithoutSpaces === placeholderTextOne))
                || (valueWithoutSpaces === placeholderTextTwo)
                || (valueWithoutSpaces === placeholderTextThree)
                || ((textarea.stripOnFocus != null) && (textarea.value === textarea.stripOnFocus))
            ) {
                textarea.value = '';
            }

            textarea.classList.remove('field-input-non-filled');
            textarea.classList.add('field-input-filled');
        },

        openEmoticonChooserWindow: function () {
            $cms.ui.open($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,emoticons}?field_name=post' + $cms.keep())), 'site_emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        },

        preventDefaultOnMobile: function (e) {
            if ($cms.isCssMode('mobile')) {
                e.preventDefault();
            }
        },

        doPostingFormPreview: function (e, btn) {
            var form = btn.form,
                url = $util.rel($cms.maintainThemeInLink($cms.getPreviewUrl() + $cms.keep()));

            $cms.form.doFormPreview(e, form, url, false, []);
        },

        moveToFullEditor: function () {
            var moreUrl = this.params.moreUrl,
                form = this.form;

            // Tell next screen what the stub to trim is
            if (form.elements['post'].defaultSubstringToStrip !== undefined) {
                if (form.elements['stub'] !== undefined) {
                    form.elements['stub'].value = form.elements['post'].defaultSubstringToStrip;
                } else {
                    if (moreUrl.includes('?')) {
                        moreUrl += '&';
                    } else {
                        moreUrl += '?';
                    }
                    moreUrl += 'stub=' + encodeURIComponent(form.elements['post'].defaultSubstringToStrip);
                }
            }

            // Try and make post reply a GET parameter
            if (form.elements['parent_id'] !== undefined) {
                if (!moreUrl.includes('?')) {
                    moreUrl += '?';
                } else {
                    moreUrl += '&';
                }
                moreUrl += 'parent_id=' + encodeURIComponent(form.elements['parent_id'].value);
            }

            // Reset form target
            form.action = moreUrl;
            form.target = '_top';

            // Handle threaded strip-on-focus
            if ((form.elements['post'].stripOnFocus !== undefined) && (form.elements['post'].value === form.elements['post'].stripOnFocus)) {
                form.elements['post'].value = '';
            }

            form.submit(); // Regular submit so that event handlers do not run (would be blocked by normal submit flow handler)
        },

        ensureRegularEditor: function () {
            var form = this.form;
            form.action = form.submitAction;
            form.target = form.submitTarget;

            // (The regular submit flow will still be happening in another event handler, so we don't need to submit here)
        },

        addCommentChecking: function (form, params) {
            if (form.extraChecks === undefined) {
                form.extraChecks = [];
            }

            form.extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
                $util.inform('Commenting: Running bespoke checks');

                if (!$cms.form.checkFieldForBlankness(form.elements['post'])) {
                    erroneous.valueOf = function () { return true; };
                    firstFieldWithError = form.elements['post'];
                    return false;
                }

                if (params.getName && !$cms.form.checkFieldForBlankness(form.elements['name'])) {
                    erroneous.valueOf = function () { return true; };
                    firstFieldWithError = form.elements['name'];
                    return false;
                }

                if (params.getTitle && !params.titleOptional && !$cms.form.checkFieldForBlankness(form.elements['title'])) {
                    erroneous.valueOf = function () { return true; };
                    firstFieldWithError = form.elements['title'];
                    return false;
                }

                if (params.getEmail && !params.emailOptional && !$cms.form.checkFieldForBlankness(form.elements['email'])) {
                    erroneous.valueOf = function () { return true; };
                    firstFieldWithError = form.elements['email'];
                    return false;
                }

                $util.inform('Commenting: Passed bespoke checks');

                return true;
            });
        },

        /* Set up a feedback form to have its CAPTCHA checked upon submission using AJAX */
        addCaptchaChecking: function (form) {
            if (form.extraChecks === undefined) {
                form.extraChecks = [];
            }
            var validValue;
            form.extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
                var value = form.elements['captcha'].value;

                if ((value === validValue) || (value === '')) {
                    return true;
                }

                return function () {
                    var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=captcha_wrong&name=' + encodeURIComponent(value) + $cms.keep();
                    return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                        if (valid) {
                            validValue = value;
                        } else {
                            $cms.functions.refreshCaptcha(document.getElementById('captcha-readable'), document.getElementById('captcha-audio'));
                        }

                        if (!valid) {
                            erroneous.valueOf = function () { return true; };
                            alerted.valueOf = function () { return true; };
                            firstFieldWithError = form.elements['captcha'];
                        }
                    });
                };
            });
        }
    });

    $cms.templates.commentAjaxHandler = function (params) {
        var urlStem = '{$FIND_SCRIPT_NOHTTP;,post_comment}?options=' + encodeURIComponent(params.options) + '&hash=' + encodeURIComponent(params.hash),
            wrapperEl = document.getElementById('comments-wrapper');

        replaceCommentsFormWithAjax(params.options, params.hash, 'comments-form', 'comments-wrapper', params.selfUrlEncoded);

        // Infinite scrolling hides the pagination when it comes into view, and auto-loads the next link, appending below the current results
        if (params.infiniteScroll) {
            $cms.ui.enableInternaliseInfiniteScrolling(urlStem, wrapperEl);
        }
    };

    /* Update a normal comments topic with AJAX replying */
    function replaceCommentsFormWithAjax(options, hash, commentsFormId, commentsWrapperId, selfUrlEncoded) {
        var commentsForm = $dom.elArg('#' + commentsFormId);

        $dom.on(commentsForm, 'submit', function commentsAjaxListener(event) {
            if (commentsForm.action !== commentsForm.submitAction) {
                return; // It's previewing or opening a full-reply URL
            }

            if (commentsForm.lastSubmitEvent && $dom.isCancelledSubmit(commentsForm.lastSubmitEvent)) {
                // Note we check on form.lastSubmitEvent rather than event, as commentsForm.lastSubmitEvent is actually the button click that validation ran on
                return;
            }

            // Cancel the event from running
            event.preventDefault();

            var commentsWrapper = $dom.$id(commentsWrapperId);
            if (!commentsWrapper) { // No AJAX, as stuff missing from template
                commentsForm.submit();
                return;
            }

            $util.inform('Commenting: AJAX submission initiated');

            var submitButton = $dom.$id('submit-button');
            if (submitButton) {
                $cms.ui.disableButton(submitButton);
            }

            // Note what posts are shown now
            var knownPostBoxIds = [];
            commentsWrapper.querySelectorAll('.box---post').forEach(function (boxEl) {
                knownPostBoxIds.push(boxEl.id);
            });

            // Fire off AJAX request
            var post = 'options=' + encodeURIComponent(options) + '&hash=' + encodeURIComponent(hash),
                postElement = commentsForm.elements['post'],
                postValue = postElement.value;

            if (postElement.defaultSubstringToStrip !== undefined) {// Strip off prefix if unchanged
                if (postValue.substring(0, postElement.defaultSubstringToStrip.length) === postElement.defaultSubstringToStrip) {
                    postValue = postValue.substring(postElement.defaultSubstringToStrip.length, postValue.length);
                }
            }
            for (var j = 0; j < commentsForm.elements.length; j++) {
                if ((commentsForm.elements[j].name) && (commentsForm.elements[j].name !== 'post')) {
                    post += '&' + commentsForm.elements[j].name + '=' + encodeURIComponent($cms.form.cleverFindValue(commentsForm, commentsForm.elements[j]));
                }
            }
            post += '&post=' + encodeURIComponent(postValue);
            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,post_comment}?self_url=' + encodeURIComponent(selfUrlEncoded) + $cms.keep(), null, post).then(function (xhr) {
                if (commentsWrapper !== document.getElementById(commentsWrapperId)) {
                    return; // No-op if comments wrapper element changed during AJAX request, e.g., slideshow loaded comments for another slide.
                }

                if ((xhr.responseText !== '') && (xhr.status !== 500)) {
                    $util.inform('Commenting: AJAX submission succeeded');

                    // Display
                    $dom.replaceWith(commentsWrapper, xhr.responseText);
                    commentsWrapper = document.getElementById(commentsWrapperId); // Because $dom.replaceWith() broke the references
                    commentsForm = document.getElementById(commentsFormId);
                    commentsForm.action = commentsForm.submitAction; // AJAX will have mangled URL (as was not running in a page context), this will fix it back

                    // Scroll back to comment
                    setTimeout(function () {
                        $dom.smoothScroll(commentsWrapper);
                    }, 0);

                    // Force reload on back button, as otherwise comment would be missing
                    forceReloadOnBack();

                    // Collapse, so user can see what happening
                    var outer = $dom.$('#comments-posting-form-outer');
                    if (outer && outer.classList.contains('toggleable-tray')) {
                        $cms.ui.toggleableTray(outer);
                    }

                    // Set fade for posts not shown before
                    commentsWrapper.querySelectorAll('.box---post').forEach(function (boxEl) {
                        if (!knownPostBoxIds.includes(boxEl.id)) {
                            boxEl.style.opacity = 0;
                            $dom.fadeTo(boxEl, null, 1);
                        }
                    });
                } else { // Error: do a normal post so error can be seen
                    $util.inform('Commenting: AJAX submission failed');

                    var tokenField = commentsForm.elements['csrf_token'];
                    if (tokenField) {
                        return $cms.getCsrfToken().then(function (text) {
                            $util.log('Regenerated CSRF token');

                            tokenField.value = text;

                            commentsForm.submit();
                        });
                    } else {
                        commentsForm.submit();
                    }
                }
            });

            commentsForm.submittedFormAlready = true;
        });
    }

    $cms.templates.ratingForm = function ratingForm(params) {
        var rating;

        if (params.error) {
            return;
        }

        for (var key in params.allRatingCriteria) {
            rating = objVal(params.allRatingCriteria[key]);

            applyRatingHighlightAndAjaxCode((rating.likes === 1), rating.rating, params.feedbackType, params.id, rating.type, rating.rating, rating.contentUrl, params.contentTitle, true);
        }
    };

    $cms.templates.postChildLoadLink = function (params, container) {
        var ids = strVal(params.implodedIds),
            id = strVal(params.id);

        $dom.on(container, 'click', '.js-click-threaded-load-more', function () {
            /* Load more from a threaded topic */
            $cms.loadSnippet('comments&id=' + encodeURIComponent(id) + '&ids=' + encodeURIComponent(ids) + '&serialized_options=' + encodeURIComponent(window.commentsSerializedOptions) + '&hash=' + encodeURIComponent(window.commentsHash)).then(function (html) {
                var wrapper;
                if (id !== '') {
                    wrapper = $dom.$('#post-children-' + id);
                } else {
                    wrapper = container.parentNode;
                }
                container.remove();

                $dom.append(wrapper, html);

                setTimeout(function () {
                    var _ids = ids.split(',');
                    for (var i = 0; i < _ids.length; i++) {
                        var element = document.getElementById('post-wrap-' + _ids[i]);
                        if (element) {
                            $dom.fadeIn(element);
                        }
                    }
                }, 0);
            });
        });

    };

    function forceReloadOnBack() {
        $dom.on(window, 'pageshow', function () {
            window.location.reload();
        });
    }

    $cms.templates.commentsWrapper = function (params, container) {
        if ((params.serializedOptions !== undefined) && (params.hash !== undefined)) {
            window.commentsSerializedOptions = params.serializedOptions;
            window.commentsHash = params.hash;
        }

        $dom.on(container, 'change', '.js-change-select-submit-form', function (e, select) {
            select.form.submit();
        });
    };

    function applyRatingHighlightAndAjaxCode(likes, initialRating, feedbackType, id, type, rating, contentUrl, contentTitle, initialisationPhase, visualOnly) {
        feedbackType = strVal(feedbackType);
        id = strVal(id);
        type = strVal(type);
        rating = Number(rating) || 0;
        visualOnly = Boolean(visualOnly);

        [1, 2, 3, 4, 5, 6, 7, 8, 9, 10].forEach(function (number) {
            var bit = $dom.$('#rating-bar-' + number + '--' + feedbackType + '--' + type + '--' + id);
            if (!bit) {
                return;
            }
            var shouldHighlight = (likes ? (rating === number) : (rating >= number));
            bit.classList.toggle('rating-star-highlight', shouldHighlight);
            bit.classList.toggle('rating-star', !shouldHighlight);

            if (!initialisationPhase) {
                return;
            }

            bit.addEventListener('mouseover', function () {
                applyRatingHighlightAndAjaxCode(likes, initialRating, feedbackType, id, type, number, contentUrl, contentTitle, false);
            });
            bit.addEventListener('mouseout', function () {
                applyRatingHighlightAndAjaxCode(likes, initialRating, feedbackType, id, type, initialRating, contentUrl, contentTitle, false);
            });

            if (visualOnly) {
                return;
            }

            bit.addEventListener('click', function (event) {
                event.preventDefault();

                // Find where the rating replacement will go
                var template = '';
                var replaceSpot = bit.parentElement;
                while (replaceSpot !== null) {
                    if (replaceSpot.classList.contains('rating-box')) {
                        template = 'RATING_BOX';
                        break;
                    }
                    if (replaceSpot.classList.contains('rating-inline-static')) {
                        template = 'RATING_INLINE_STATIC';
                        break;
                    }
                    if (replaceSpot.classList.contains('rating-inline-dynamic')) {
                        template = 'RATING_INLINE_DYNAMIC';
                        break;
                    }

                    replaceSpot = replaceSpot.parentElement;
                }
                var _replaceSpot = (template === '') ? bit.parentNode.parentNode.parentNode.parentNode : replaceSpot;

                // Show loading animation
                $dom.html(_replaceSpot, '');
                var loadingImage = document.createElement('img');
                loadingImage.className = 'ajax-loading';
                loadingImage.src = $util.srl('{$IMG;,loading}');
                loadingImage.width = '20';
                loadingImage.height = '20';
                loadingImage.style.height = '12px';
                _replaceSpot.appendChild(loadingImage);

                // AJAX call
                var snippetRequest = 'rating&type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id) + '&feedback_type=' + encodeURIComponent(feedbackType) + '&template=' + encodeURIComponent(template) + '&content_url=' + encodeURIComponent($cms.protectURLParameter(contentUrl)) + '&content_title=' + encodeURIComponent(contentTitle);

                $cms.loadSnippet(snippetRequest, 'rating=' + encodeURIComponent(number)).then(function (message) {
                    $dom.replaceWith(_replaceSpot, (template === '') ? ('<strong>' + message + '</strong>') : message);
                });
            });
        });
    }
}(window.$cms, window.$util, window.$dom));
