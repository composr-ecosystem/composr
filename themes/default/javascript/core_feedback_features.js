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

        this.form = this.$('form.js-form-comments');
        this.btnSubmit = this.$('.js-btn-submit-comments');

        $cms.requireJavascript(['jquery', 'jquery_autocomplete']).then(function () {
            window.$jqueryAutocomplete.setUpComcodeAutocomplete('post', Boolean(params.wysiwyg));
        });

        if ($cms.configOption('enable_previews') && $cms.isForcePreviews()) {
            this.btnSubmit.style.display = 'none';
        }

        if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '')) {
            this.addCaptchaChecking();
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
                'click .js-btn-submit-comments': 'clickBtnSubmit',
                'click .js-click-do-form-preview': 'doPostingFormPreview',
                'submit .js-form-comments': 'submitFormComments',

                'click .js-img-review-bar': 'reviewBarClick',
                'mouseover .js-img-review-bar': 'reviewBarHover',
                'mouseout .js-img-review-bar': 'reviewBarHover',

                'click .js-click-play-self-audio-link': 'playSelfAudioLink',

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

        playSelfAudioLink: function (e, link, soundObject) {
            e.preventDefault();
            $cms.playSelfAudioLink(link, soundObject);
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

        clickBtnSubmit: function (e, button) {
            var form = button.form;

            form.target = ''; // Might have been set to 'preview-iframe' by $cms.form.doFormPreview()

            // if (form.oldAction !== undefined) {
            //     form.action = form.oldAction;
            // }
            //
            // if ($dom.trigger(form, 'submit') !== false) {
            //     $cms.ui.disableButton(button);
            //     form.submit();
            // }

            $cms.form.doFormSubmit(form);
        },

        doPostingFormPreview: function (e, btn) {
            var form = btn.form,
                url = $util.rel($cms.maintainThemeInLink($cms.getPreviewUrl() + $cms.keep()));

            $cms.form.doFormPreview(form, url);
        },

        submitFormComments: function (e) {
            var form = this.form,
                params = this.params;

            if ((params.moreUrl !== undefined) && (form.action === params.moreUrl)) {
                return;
            }

            if (!$cms.form.checkFieldForBlankness(form.elements.post)) {
                e.preventDefault();
                return;
            }

            if (params.getName && !$cms.form.checkFieldForBlankness(form.elements['name'])) {
                e.preventDefault();
                return;
            }

            if (params.getTitle && !params.titleOptional && !$cms.form.checkFieldForBlankness(form.elements.title)) {
                e.preventDefault();
                return;
            }

            if (params.getEmail && !params.emailOptional && !$cms.form.checkFieldForBlankness(form.elements.email)) {
                e.preventDefault();
                return;
            }

            if (params.analyticEventCategory) {
                e.preventDefault();
                $cms.statsEventTrack(null, params.analyticEventCategory).then(function () {
                    $dom.submit(form);
                });
            }
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
            form.target = '_top';
            if (form.oldAction !== undefined) {
                form.oldAction = form.action;
            }
            form.action = moreUrl;

            // Handle threaded strip-on-focus
            if ((form.elements['post'].stripOnFocus !== undefined) && (form.elements['post'].value === form.elements['post'].stripOnFocus)) {
                form.elements['post'].value = '';
            }

            $dom.submit(form);
        },

        /* Set up a form to have its CAPTCHA checked upon submission using AJAX */
        addCaptchaChecking: function () {
            var form = this.form,
                submitBtn = form.elements['submit_button'],
                validValue;

            form.addEventListener('submit', function submitCheck(e) {
                var value = form.elements['captcha'].value;

                if (value === validValue) {
                    return;
                }

                submitBtn.disabled = true;
                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=captcha_wrong&name=' + encodeURIComponent(value) + $cms.keep();
                e.preventDefault();
                $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                        $dom.submit(form);
                    } else {
                        var image = document.getElementById('captcha-image');
                        if (!image) {
                            image = document.getElementById('captcha-frame');
                        }
                        image.src += '&'; // Force it to reload latest captcha
                        submitBtn.disabled = false;
                    }
                });
            });

            window.addEventListener('pageshow', function () {
                var image = document.getElementById('captcha-image');
                if (!image) {
                    image = document.getElementById('captcha-frame');
                }
                image.src += '&'; // Force it to reload latest captcha
            });
        }
    });

    $cms.templates.ratingForm = function ratingForm(params) {
        var rating;

        if (params.error) {
            return;
        }

        for (var key in params.allRatingCriteria) {
            rating = objVal(params.allRatingCriteria[key]);

            applyRatingHighlightAndAjaxCode((rating.likes === 1), rating.rating, params.contentType, params.id, rating.type, rating.rating, rating.contentUrl, rating.contentTitle, true);
        }
    };

    $cms.templates.commentsWrapper = function (params, container) {
        if ((params.serializedOptions !== undefined) && (params.hash !== undefined)) {
            window.commentsSerializedOptions = params.serializedOptions;
            window.commentsHash = params.hash;
        }

        $dom.on(container, 'change', '.js-change-select-submit-form', function (e, select) {
            $dom.submit(select.form);
        });
    };

    $cms.templates.commentAjaxHandler = function (params) {
        var urlStem = '{$FIND_SCRIPT_NOHTTP;,post_comment}?options=' + encodeURIComponent(params.options) + '&hash=' + encodeURIComponent(params.hash),
            wrapperEl = document.getElementById('comments-wrapper');

        replaceCommentsFormWithAjax(params.options, params.hash, 'comments-form', 'comments-wrapper');

        // Infinite scrolling hides the pagination when it comes into view, and auto-loads the next link, appending below the current results
        if (params.infiniteScroll) {
            $dom.enableInternaliseInfiniteScrolling(urlStem, wrapperEl);
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

    /* Update a normal comments topic with AJAX replying */
    function replaceCommentsFormWithAjax(options, hash, commentsFormId, commentsWrapperId) {
        var commentsForm = $dom.elArg('#' + commentsFormId);

        $dom.on(commentsForm, 'submit', function commentsAjaxListener(event) {
            var defaultNotPrevented;

            if (event.detail && (event.detail.triggeredByDoFormPreview || event.detail.triggeredByCommentsAjaxListener)) {
                return;
            }

            // Cancel the event from running
            event.preventDefault();

            defaultNotPrevented = $dom.trigger(commentsForm, 'submit', { detail: { triggeredByCommentsAjaxListener: true } });

            if (defaultNotPrevented === false) {
                return false;
            }

            var commentsWrapper = $dom.$id(commentsWrapperId);
            if (!commentsWrapper) { // No AJAX, as stuff missing from template
                commentsForm.submit();
                return;
            }

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
            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,post_comment}' + $cms.keep(true), null, post).then(function (xhr) {
                if (commentsWrapper !== document.getElementById(commentsWrapperId)) {
                    return; // No-op if comments wrapper element changed during AJAX request, e.g., slideshow loaded comments for another slide.
                }

                if ((xhr.responseText !== '') && (xhr.status !== 500)) {
                    // Display
                    var oldAction = commentsForm.action;
                    $dom.replaceWith(commentsWrapper, xhr.responseText);
                    commentsWrapper = document.getElementById(commentsWrapperId); // Because $dom.replaceWith() broke the references
                    commentsForm = document.getElementById(commentsFormId);
                    commentsForm.action = oldAction; // AJAX will have mangled URL (as was not running in a page context), this will fix it back

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
                    commentsForm.submit();
                }
            });

            return false;
        });
    }

    function applyRatingHighlightAndAjaxCode(likes, initialRating, contentType, id, type, rating, contentUrl, contentTitle, initialisationPhase, visualOnly) {
        contentType = strVal(contentType);
        id = strVal(id);
        type = strVal(type);
        rating = Number(rating) || 0;
        visualOnly = Boolean(visualOnly);

        [1, 2, 3, 4, 5, 6, 7, 8, 9, 10].forEach(function (number) {
            var bit = $dom.$('#rating-bar-' + number + '--' + contentType + '--' + type + '--' + id);
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
                applyRatingHighlightAndAjaxCode(likes, initialRating, contentType, id, type, number, contentUrl, contentTitle, false);
            });
            bit.addEventListener('mouseout', function () {
                applyRatingHighlightAndAjaxCode(likes, initialRating, contentType, id, type, initialRating, contentUrl, contentTitle, false);
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
                var snippetRequest = 'rating&type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id) + '&content_type=' + encodeURIComponent(contentType) + '&template=' + encodeURIComponent(template) + '&content_url=' + encodeURIComponent($cms.protectURLParameter(contentUrl)) + '&content_title=' + encodeURIComponent(contentTitle);

                $cms.loadSnippet(snippetRequest, 'rating=' + encodeURIComponent(number)).then(function (message) {
                    $dom.replaceWith(_replaceSpot, (template === '') ? ('<strong>' + message + '</strong>') : message);
                });
                return false;
            });
        });
    }

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

        var post = form.elements.post;

        $dom.smoothScroll($dom.findPosY(form, true));

        var outer = $dom.$('#comments-posting-form-outer');
        if (outer && $dom.notDisplayed(outer)) {
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

        $cms.manageScrollHeight(post);
        post.scrollTop = post.scrollHeight;
    };
}(window.$cms, window.$util, window.$dom));
