(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.Attachment = Attachment;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function Attachment(params) {
        Attachment.base(this, 'constructor', arguments);

        if ($cms.configOption('complex_uploader')) {
            window.$plupload.preinitFileInput("attachment_multi", "file" + params.i, params.postingFieldName, params.filter);
        }

        if (params.syndicationJson != null) {
            $cms.requireJavascript('editing').then(function () {
                window.$editing.showUploadSyndicationOptions("file" + params.i, params.syndicationJson, Boolean(params.noQuota));
            });
        }
    }

    $util.inherits(Attachment, $cms.View, /**@lends Attachment#*/{
        events: function () {
            return {
                'change .js-inp-file-change-set-attachment': 'setAttachment'
            };
        },

        setAttachment: function () {
            window.$posting.setAttachment('post', this.params.i, '');
        }
    });

    $cms.views.Carousel = Carousel;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function Carousel(params) {
        Carousel.base(this, 'constructor', arguments);

        var carouselId = strVal(params.carouselId),
            carouselNs = document.getElementById('carousel-ns-' + carouselId);

        this.mainEl = this.$('.main');
        this.mainEl.appendChild(carouselNs);

        $dom.show(this.el);
    }

    $util.inherits(Carousel, $cms.View, /**@lends Carousel#*/{
        events: function () {
            return {
                'mousedown .js-btn-car-move': 'move',
                'keypress .js-btn-car-move': 'move'
            };
        },

        move: function (e, btn) {
            var self = this,
                amount = btn.dataset.moveAmount;

            setTimeout(function () {
                self.carouselMove(amount);
            }, 10);
        },

        carouselMove: function (amount) {
            amount = Number(amount);

            if (amount > 0) {
                this.mainEl.scrollLeft += 3;
                amount--;
                if (amount < 0) {
                    amount = 0;
                }
            } else {
                this.mainEl.scrollLeft -= 3;
                amount++;
                if (amount > 0) {
                    amount = 0;
                }
            }

            var that = this;
            if (amount !== 0) {
                setTimeout(function () {
                    that.carouselMove(amount);
                }, 10);
            }
        }
    });

    $cms.views.ComcodeMediaSet = ComcodeMediaSet;
    /**
     * @memberof $cms.views
     * @class $cms.views.ComcodeMediaSet
     * @extends $cms.View
     */
    function ComcodeMediaSet(params) {
        ComcodeMediaSet.base(this, 'constructor', arguments);

        if ($cms.configOption('js_overlays')) {
            this.setup(params);
        }
    }

    $util.inherits(ComcodeMediaSet, $cms.View, /**@lends $cms.views.ComcodeMediaSet#*/{
        setup: function (params) {
            var imgs = window['imgs_' + params.rand] = [],
                imgsThumbs = window['imgs_thumbs_' + params.rand] = [],
                setImgWidthHeight = false,
                mediaSet = $dom.$id('media_set_' + params.rand),
                as = window.as = mediaSet.querySelectorAll('a, video'),
                containsVideo = false,
                thumbWidthConfig = $cms.configOption('thumb_width') + 'x' + $cms.configOption('thumb_width'),
                i, x;

            if ((thumbWidthConfig !== 'x') && ((params.width + 'x' + params.height) !== 'x')) {
                setImgWidthHeight = true;
            }

            x = 0;
            for (i = 0; i < as.length; i++) {
                if (as[i].localName === 'video') {
                    var span = as[i].querySelector('span'),
                        title = '';

                    if (span) {
                        title = $dom.html(span);
                        span.parentNode.removeChild(span);
                    }

                    imgs.push([$dom.html(as[i]), title, true]);
                    imgsThumbs.push(as[i].poster || $util.srl('{$IMG^;,video_thumb}'));

                    containsVideo = true;

                    x++;

                } else if ((as[i].children.length === 1) && (as[i].firstElementChild.localName === 'img')) {
                    as[i].title = as[i].title.replace('{!LINK_NEW_WINDOW^;}', '').replace(/^\s+/, '');

                    imgs.push([as[i].href, (as[i].title === '') ? as[i].firstElementChild.alt : as[i].title, false]);
                    imgsThumbs.push(as[i].firstElementChild.src);

                    as[i].addEventListener('click', (function (x) {
                        openImageIntoLightbox(imgs, x);
                        return false;
                    }).bind(undefined, x));

                    if (as[i].rel) {
                        as[i].rel = as[i].rel.replace('lightbox', '');
                    }

                    x++;
                }
            }

            // If you only want a single image-based thumbnail
            if (containsVideo) { // Remove this 'if' (so it always runs) if you do not want the grid-style layout (plus remove the media-set class from the outer div
                var width = params.width ? 'style="width: ' + Number(params.width) + 'px"' : '',
                    imgWidthHeight = setImgWidthHeight ? ' width="' + Number(params.width) + '" height="' + Number(params.height) + '"' : '',
                    mediaSetHtml = /** @lang HTML */'' +
                        '<figure class="attachment" ' + width + '>' +
                        '   <figcaption>' + $util.format('{!comcode:MEDIA_SET;^}', [imgs.length]) + '</figcaption>' +
                        '   <div>' +
                        '        <div class="attachment-details">' +
                        '            <a class="js-click-open-images-into-lightbox" target="_blank" title="' + $cms.filter.html($util.format('{!comcode:MEDIA_SET^;}', [imgs.length])) + ' {!LINK_NEW_WINDOW^/}" href="#!">' +
                        '                <img ' + imgWidthHeight + ' src="' + $cms.filter.html(imgsThumbs[0]) + '">' +
                        '            </a>' +
                        '        </div>' +
                        '    </div>' +
                        '</figure>';
                $dom.html(mediaSet, mediaSetHtml);
                $dom.on(mediaSet.querySelector('.js-click-open-images-into-lightbox'), 'click', function () {
                    openImageIntoLightbox(imgs);
                });
            }

            function openImageIntoLightbox(imgs, start) {
                start = Number(start) || 0;

                var modal = $cms.ui.openImageIntoLightbox(imgs[start][0], imgs[start][1], start + 1, imgs.length, true, imgs[start][2]);
                modal.positionInSet = start;

                // class="previous-button next-button"
                // ^ Above comment serves to mark the classes as _used_ for the 'css_file' unit test

                var previousButton = document.createElement('img');
                previousButton.className = 'previous-button';
                previousButton.src = $util.srl('{$IMG;,icons/media_set/previous}');
                previousButton.width = '74';
                previousButton.height = '74';
                previousButton.addEventListener('click', clickPreviousButton);
                function clickPreviousButton() {
                    var newPosition = modal.positionInSet - 1;
                    if (newPosition < 0) {
                        newPosition = imgs.length - 1;
                    }
                    modal.positionInSet = newPosition;
                    _openDifferentImageIntoLightbox(modal, newPosition, imgs);
                }

                modal.left = clickPreviousButton;
                modal.el.firstElementChild.appendChild(previousButton);

                var nextButton = document.createElement('img');
                nextButton.className = 'next-button';
                nextButton.src = $util.srl('{$IMG;,icons/media_set/next}');
                nextButton.width = '74';
                nextButton.height = '74';
                nextButton.addEventListener('click', clickNextButton);
                function clickNextButton() {
                    var newPosition = modal.positionInSet + 1;
                    if (newPosition >= imgs.length) {
                        newPosition = 0;
                    }
                    modal.positionInSet = newPosition;
                    _openDifferentImageIntoLightbox(modal, newPosition, imgs);
                }

                modal.right = clickNextButton;
                modal.el.firstElementChild.appendChild(nextButton);

                function _openDifferentImageIntoLightbox(modal, position, imgs) {
                    var isVideo = imgs[position][2];

                    // Load proper image
                    setTimeout(function () { // Defer execution until the HTML was parsed
                        if (isVideo) {
                            var video = document.createElement('video');
                            video.id = 'lightbox-image';
                            video.className = 'lightbox-image';
                            video.controls = 'controls';
                            video.autoplay = 'autoplay';
                            $dom.html(video, imgs[position][0]);
                            video.addEventListener('loadedmetadata', function () {
                                $cms.ui.resizeLightboxDimensionsImg(modal, video, true, true);
                            });
                        } else {
                            var img = modal.topWindow.document.createElement('img');
                            img.className = 'lightbox-image';
                            img.id = 'lightbox-image';
                            img.src = '{$IMG_INLINE;,loading}';
                            img.width = '20';
                            img.height = '20';
                            setTimeout(function () { // Defer execution until after loading is set
                                img.addEventListener('load', function () {
                                    $cms.ui.resizeLightboxDimensionsImg(modal, img, true, isVideo);
                                });
                                img.src = imgs[position][0];
                            }, 0);
                        }

                        var lightboxDescription = modal.topWindow.$dom.$id('lightbox-description'),
                            lightboxPositionInSetX = modal.topWindow.$dom.$id('lightbox-position-in-set-x');

                        if (lightboxDescription) {
                            $dom.html(lightboxDescription, imgs[position][1]);
                        }

                        if (lightboxPositionInSetX) {
                            $dom.html(lightboxPositionInSetX, position + 1);
                        }
                    });
                }

            }
        }
    });

    $cms.views.AttachmentsBrowser = AttachmentsBrowser;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function AttachmentsBrowser() {
        AttachmentsBrowser.base(this, 'constructor', arguments);
    }

    $util.inherits(AttachmentsBrowser, $cms.View, /**@lends AttachmentsBrowser#*/{
        events: function () {
            return {
                'click .js-click-do-attachment-and-close': 'doAttachmentAndClose'
            };
        },
        doAttachmentAndClose: function () {
            var params = this.params,
                fieldName = params.fieldName || '',
                id = params.id || '',
                description = params.description || '';

            window.$editing.doAttachment(fieldName, id, description).then(function () {
                window.fauxClose ? window.fauxClose() : window.close();
            });
        }
    });

    $cms.functions.comcodeToolsComcodeConvertScript = function comcodeToolsComcodeConvertScript() {
        var form = $dom.$('#semihtml').form;

        form.elements['from_html'][0].addEventListener('click', refreshLockedInputs);
        form.elements['from_html'][1].addEventListener('click', refreshLockedInputs);
        form.elements['from_html'][2].addEventListener('click', refreshLockedInputs);

        function refreshLockedInputs() {
            var value = Number($cms.form.radioValue(form.elements['from_html']));
            $dom.$('#semihtml').disabled = (value !== 0);
            $dom.$('#is_semihtml').disabled = (value !== 0);
            $dom.$('#lax').disabled = (value !== 0);
            $dom.$('#fix_bad_html').disabled = (value === 1);
            $dom.$('#force').disabled = (value !== 1);
        }
    };

    $cms.functions.comcodeAddTryForSpecialComcodeTag = function comcodeAddTryForSpecialComcodeTag() {
        document.getElementById('framed').addEventListener('change', function () {
            if (this.checked && document.getElementById('_safe')) {
                document.getElementById('_safe').checked = false;
            }
        });
    };

    $cms.templates.comcodePageEditScreen = function comcodePageEditScreen(params, container) {
        $dom.on(container, 'click', '.js-btn-delete-page', function (e, btn) {
            var form = btn.form;
            var deleteField = form.elements['delete'];

            $cms.ui.confirm('{!_ARE_YOU_SURE_DELETE;^}', function (result) {
                if (result) {
                    deleteField.value = '1';
                    form.submit();
                }
            }, '{!CONFIRM_TEXT;^}');
        });
    };

    $cms.templates.comcodeMemberLink = function comcodeMemberLink(params, container) {
        var loadTooltipPromise = null;

        $dom.on(container, 'mouseover', '.js-comcode-member-link', activateComcodeMemberLink);
        $dom.on(container, 'focusin', '.js-comcode-member-link', activateComcodeMemberLink);

        function activateComcodeMemberLink(e, el) {
            el.cancelled = false;

            if (loadTooltipPromise == null) {
                loadTooltipPromise = $cms.loadSnippet('member_tooltip&member_id=' + params.memberId);
            }

            loadTooltipPromise.then(function (result) {
                if (!el.cancelled) {
                    $cms.ui.activateTooltip(el, e, result, 'auto', null, null, false, 0);
                }
            });
        }

        $dom.on(container, 'mouseout focusout', '.js-comcode-member-link', function (e, el) {
            if (el.contains(e.relatedTarget)) {
                return;
            }

            $cms.ui.deactivateTooltip(el);
            el.cancelled = true;
        });
    };

    $cms.templates.comcodeMessage = function comcodeMessage(params, container) {
        var name = strVal(params.name);

        $dom.on(container, 'click', '.js-link-click-open-emoticon-chooser-window', function (e, link) {
            var url = $util.rel($cms.maintainThemeInLink(link.href));
            $cms.ui.open(url, 'field_emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        });

        $dom.on(container, 'click', '.js-click-toggle-wysiwyg', function () {
            window.$editing.toggleWysiwyg(name);
        });
    };

    $cms.templates.attachments = function attachments(params, container) {
        window.attachmentTemplate = strVal(params.attachmentTemplate);
        window.maxAttachments = Number(params.maxAttachments) || 0;
        window.numAttachments = Number(params.numAttachments) || 0;

        var postingFieldName = strVal(params.postingFieldName);

        if ($cms.browserMatches('simplified_attachments_ui')) {
            window.numAttachments = 1;
            window.rebuildAttachmentButtonForNext = rebuildAttachmentButtonForNext; // Must only be defined when 'simplified_attachments_ui' is enabled

            $dom.load.then(function () {
                var attachmentBrowseButton = document.getElementById('js-attachment-browse-button--' + postingFieldName);

                if (attachmentBrowseButton && (attachmentBrowseButton.classList.contains('for-field-' + postingFieldName))) {
                    // Attach Plupload with #js-attachment-browse-button as browse button
                    window.rebuildAttachmentButtonForNext(attachmentBrowseButton);
                }
            });
        } else {
            $dom.on(container, 'click', '.js-click-open-attachment-popup', function (e, link) {
                e.preventDefault();
                $cms.ui.open($util.rel($cms.maintainThemeInLink(link.href)), 'site_attachment_chooser', 'width=550,height=600,status=no,resizable=yes,scrollbars=yes');
            });
        }


        var lastAttachmentBrowseButton;

        /**
         * Bind Plupload to the specified browse button (`attachmentBrowseButton`)
         * @param _postingFieldName
         * @param attachmentBrowseButton
         */
        function rebuildAttachmentButtonForNext(attachmentBrowseButton) {
            if (attachmentBrowseButton === undefined) {
                attachmentBrowseButton = lastAttachmentBrowseButton; // Use what was used last time
            }

            lastAttachmentBrowseButton = attachmentBrowseButton;

            $cms.requireJavascript('plupload').then(function () {
                attachmentBrowseButton.style.display = '';
                window.$plupload.prepareSimplifiedFileInput('attachment_multi', 'file' + window.numAttachments, postingFieldName, strVal(params.filter), attachmentBrowseButton);
            });
        }
    };

    $cms.templates.comcodeImg = function comcodeImg(params) {
        var img = this,
            refreshTime = Number(params.refreshTime) || 0;

        if ((typeof params.rollover === 'string') && (params.rollover !== '')) {
            $cms.ui.createRollover(img.id, params.rollover);
        }

        if (refreshTime > 0) {
            setInterval(function () {
                if (!img.timer) {
                    img.timer = 0;
                }
                img.timer += refreshTime;

                if (img.src.indexOf('?') === -1) {
                    img.src += '?time=' + img.timer;
                } else if (img.src.indexOf('time=') === -1) {
                    img.src += '&time=' + img.timer;
                } else {
                    img.src = img.src.replace(/time=\d+/, 'time=' + img.timer);
                }
            }, refreshTime);
        }
    };

    $cms.templates.comcodeEditorButton = function comcodeEditorButton(params, btn) {
        var isPostingField = Boolean(params.isPostingField),
            b = strVal(params.b),
            fieldName = strVal(params.fieldName);

        $dom.on(btn, 'click', function () {
            var mainWindow = btn.ownerDocument.defaultView;

            if ($cms.browserMatches('simplified_attachments_ui') && isPostingField && ((b === 'thumb') || (b === 'img'))) {
                return;
            }

            mainWindow['doInput' + $util.ucFirst($util.camelCase(b))](fieldName);
        });
    };

    $cms.templates.comcodeRandom = function comcodeRandom(params) {
        var rand, part, num, use, comcoderandom;

        rand = Math.random() * params.max;

        for (var key in params.parts) {
            part = params.parts[key];
            num = part.num;
            use = part.val;

            if (num > rand) {
                break;
            }
        }

        comcoderandom = document.getElementById('comcoderandom' + params.randIdRandom);
        $dom.html(comcoderandom, use);
    };

    $cms.templates.emoticonClickCode = function emoticonClickCode(params, container) {
        var fieldName = strVal(params.fieldName);

        $dom.on(container, 'click', function (e) {
            e.preventDefault();
            window.$editing.doEmoticon(fieldName, container, false);
        });
    };

    $cms.templates.comcodeOverlay = function comcodeOverlay(params, container) {
        var id = strVal(params.id),
            timeout = Number(params.timeout),
            timein = Number(params.timein);

        $dom.on(container, 'click', '.js-click-dismiss-overlay', function () {
            var bi = document.getElementById('main-website-inner');
            if (bi) {
                bi.classList.remove('faded');
            }

            document.getElementById(params.randIdOverlay).style.display = 'none';

            if (id) {
                $cms.setCookie('og_' + id, '1', 365);
            }
        });

        if (!id || ($cms.readCookie('og_' + id) !== '1')) {
            setTimeout(function () {
                var element, bi;

                $dom.smoothScroll(0);

                bi = document.getElementById('main-website-inner');

                element = document.getElementById(params.randIdOverlay);
                element.style.display = 'block';
                element.parentNode.removeChild(element);
                document.body.appendChild(element);

                if (bi) {
                    bi.style.left = (Number(params.x) + $dom.findPosX(bi, true)) + 'px';
                    bi.classList.add('faded');
                }

                $dom.fadeIn(element);


                if (timeout !== -1) {
                    setTimeout(function () {
                        if (bi) {
                            bi.classList.remove('faded');
                        }

                        if (element) {
                            element.style.display = 'none';
                        }
                    }, timeout);
                }
            }, timein + 100);
        }
    };

    $cms.templates.comcodeTabHead = function comcodeTabHead(params, container) {
        var tabSets = $cms.filter.id(params.tabSets),
            title = $cms.filter.id(params.title);

        $dom.on(container, 'click', function () {
            $cms.ui.selectTab('g', tabSets + '-' + title);
        });
    };

    $cms.templates.comcodeTabBody = function (params, container) {
        var tabSets = $cms.filter.id(params.tabSets),
            title = $cms.filter.id(params.title);

        if (params.blockCallUrl) {
            window['load_tab__' + title] = function () {
                $cms.callBlock(params.blockCallUrl, '', document.getElementById('g_' + title), false, null, false, null, true);
            };
        }

        if (container.style.display === 'block') {
            // Comcode may specify different default which is only known to COMCODE_TAB_BODY template, and needs to be handled for COMCODE_TAB_HEAD
            $cms.ui.selectTab('g', tabSets + '-' + title, false, true);
        }
    };

    $cms.views.ComcodeBigTabsController = ComcodeBigTabsController;
    /**
     * @memberof $cms.views
     * @class $cms.views.ComcodeBigTabsController
     * @extends $cms.View
     */
    function ComcodeBigTabsController(params) {
        ComcodeBigTabsController.base(this, 'constructor', arguments);

        var passId = this.passId = $cms.filter.id(params.passId),
            id = this.id = passId + ((params.bigTabSets === undefined) ? '' : ('-' + params.bigTabSets)),
            sections = this.sections = params.tabs.map($cms.filter.id),
            switchTime = this.switchTime = params.switchTime;

        /* Precache images */
        new Image().src = $util.srl('{$IMG;,big_tabs/controller_button}');
        new Image().src = $util.srl('{$IMG;,big_tabs/controller_button_active}');

        if (switchTime !== undefined) {
            flipPage(0, id, sections, switchTime);
        }
    }

    $util.inherits(ComcodeBigTabsController, $cms.View, /**@lends $cms.views.ComcodeBigTabsController#*/{
        events: function events() {
            return {
                'click .js-onclick-flip-page': 'doFlipPage'
            };
        },

        doFlipPage: function doFlipPage(e, clicked) {
            var flipTo = clicked.dataset.vwFlipTo;
            flipPage(flipTo, this.id, this.sections, this.switchTime);
        }
    });

    $cms.views.ComcodeSectionController = ComcodeSectionController;
    /**
     * @memberof $cms.views
     * @class $cms.views.ComcodeSectionController
     * @extends $cms.View
     */
    function ComcodeSectionController(params) {
        ComcodeSectionController.base(this, 'constructor', arguments);

        this.passId = $cms.filter.id(params.passId);
        this.sections = params.sections.map($cms.filter.id);

        flipPage(0, this.passId, this.sections);
    }

    $util.inherits(ComcodeSectionController, $cms.View, /**@lends $cms.views.ComcodeSectionController#*/{
        events: function events() {
            return {
                'click .js-click-flip-page': 'doFlipPage'
            };
        },

        doFlipPage: function doFlipPage(e, clicked) {
            var flipTo = clicked.dataset.vwFlipTo;
            flipPage(flipTo, this.passId, this.sections);
        }
    });

    var _flipPageTimeouts = {};
    function flipPage(to, id, sections, switchTime) {
        var i, currentPos = 0, section;

        if (_flipPageTimeouts[id]) {
            clearTimeout(_flipPageTimeouts[id]);
            delete _flipPageTimeouts[id];
        }

        if ($util.isNumeric(to)) {
            to = Number(to);

            for (i = 0; i < sections.length; i++) {
                section = document.getElementById(id + '-section-' + sections[i]);
                if (section) {
                    if ((section.style.display === 'block') && (section.style.position !== 'absolute')) {
                        currentPos = i;
                        break;
                    }
                }
            }

            currentPos += to;
        } else {
            for (i = 0; i < sections.length; i++) {
                if (sections[i] === to) {
                    currentPos = i;
                    break;
                }
            }
        }

        // Previous/next updates
        var el;
        el = document.getElementById(id + '-has-next-yes');
        if (el) {
            el.style.display = (currentPos === (sections.length - 1)) ? 'none' : 'inline-block';
        }
        el = document.getElementById(id + '-has-next-no');
        if (el) {
            el.style.display = (currentPos === (sections.length - 1)) ? 'inline-block' : 'none';
        }
        el = document.getElementById(id + '-has-previous-yes');
        if (el) {
            el.style.display = (currentPos === 0) ? 'none' : 'inline-block';
        }
        el = document.getElementById(id + '-has-previous-no');
        if (el) {
            el.style.display = (currentPos === 0) ? 'inline-block' : 'none';
        }

        // We make our forthcoming one instantly visible to stop the browser possibly scrolling up if there is a tiny time interval when none are visible
        el = document.getElementById(id + '-section-' + sections[i]);
        if (el) {
            el.style.display = 'block';
        }

        for (i = 0; i < sections.length; i++) {
            el = document.getElementById(id + '-goto-' + sections[i]);
            if (el) {
                el.style.display = (i === currentPos) ? 'none' : 'inline-block';
            }

            el = document.getElementById(id + '-btgoto-' + sections[i]);
            if (el) {
                el.classList.toggle('big-tab-active', (i === currentPos));
                el.classList.toggle('big-tab-inactive', (i !== currentPos));
            }

            el = document.getElementById(id + '-isat-' + sections[i]);
            if (el) {
                el.style.display = (i === currentPos) ? 'inline-block' : 'none';
            }

            el = document.getElementById(id + '-section-' + sections[i]);
            if (el) {
                if (el.classList.contains('comcode-big-tab')) {
                    if (i === currentPos) {
                        el.style.width = '';
                        el.style.position = 'static';
                        el.style.zIndex = 10;
                        el.style.opacity = 1;
                    } else {
                        el.style.opacity = (el.style.position !== 'static') ? 0 : 1;
                        el.style.width = (el.offsetWidth - 24) + 'px'; // 24=lhs padding+rhs padding+lhs border+rhs border
                        el.style.position = 'absolute';
                        el.style.zIndex = -10;
                        el.style.top = '0';
                        el.parentNode.style.position = 'relative';
                    }
                    el.style.display = 'block';
                } else {
                    el.style.display = (i === currentPos) ? 'block' : 'none';
                }
            }
        }

        if (switchTime) {
            _flipPageTimeouts[id] = setTimeout(function () {
                var nextPage = 0, i, el;

                for (i = 0; i < sections.length; i++) {
                    el = document.getElementById(id + '-section-' + sections[i]);
                    if ((el) && (el.style.display === 'block') && (el.style.position !== 'absolute')) {
                        nextPage = i + 1;
                    }
                }

                if (nextPage === sections.length) {
                    nextPage = 0;
                }

                flipPage(sections[nextPage], id, sections, switchTime);
            }, switchTime);
        }

        return false;
    }

    $cms.templates.comcodePulse = function (params) {
        var id = 'pulse-wave-' + params.randIdPulse;

        window[id] = [0, params.maxColor, params.minColor, params.speed, []];
        setInterval(function () {
            window.$pulse.processWave(document.getElementById(id));
        }, params.speed);
    };

    $cms.templates.comcodeShocker = function (params) {
        var id = params.randIdShocker,
            parts = params.parts || [], part,
            time = Number(params.time);

        window.shockerParts || (window.shockerParts = {});
        window.shockerPos || (window.shockerPos = {});

        window.shockerParts[id] = [];
        window.shockerPos[id] = 0;

        for (var i = 0, len = parts.length; i < len; i++) {
            part = parts[i];
            window.shockerParts[id].push([part.left, part.right]);
        }

        shockerTick(id, time, params.maxColor, params.minColor);
        setInterval(function () {
            shockerTick(id, time, params.maxColor, params.minColor);
        }, time);
    };

    function shockerTick(id, time, minColor, maxColor) {
        if ((document.hidden !== undefined) && (document.hidden)) {
            return;
        }

        if (window.shockerPos[id] >= window.shockerParts[id].length) {
            window.shockerPos[id] = 0;
        }

        var eLeft = document.getElementById('comcodeshocker' + id + '-left');
        if (!eLeft) {
            return;
        }
        $dom.html(eLeft, window.shockerParts[id][window.shockerPos[id]][0]);
        $dom.fadeIn(eLeft);

        var eRight = document.getElementById('comcodeshocker' + id + '-right');
        if (!eRight) {
            return;
        }
        $dom.html(eRight, window.shockerParts[id][window.shockerPos[id]][1]);
        $dom.fadeIn(eRight);

        window.shockerPos[id]++;

        window['comcodeshocker' + id + '-left'] = [0, minColor, maxColor, time / 13, []];
        setInterval(function () {
            window.$pulse.processWave(eLeft);
        }, window['comcodeshocker' + id + '-left'][3]);
    }

    $cms.templates.comcodeTicker = function (params, container) {
        window.tickPos || (window.tickPos = {});

        var id = 'ticker-' + $util.random(),
            width = params.width,
            dynamicWidth = (width === ''),
            speed = params.speed;

        if (dynamicWidth) {
            container.style.display = 'block';
            width = $dom.width(container);

            $dom.on(window, 'resize', function () {
                window.setTimeout(function () {
                    width = $dom.width(container);
                    window.tickPos[id] = width;
                    container.childNodes[0].style.width = width + 'px';
                }, 0);
            });
        }

        window.tickPos[id] = width;
        $dom.html(container, '<div class="ticker" style="text-indent: ' + width + 'px; width: ' + width + 'px;" id="' + id + '"><span>' +
            $cms.filter.nl(params.text) + '</span></div>'
        );

        var lastTime = 0;
        var animationFunc = function (timestamp) {
            if (timestamp - lastTime >= 1000 / speed) {
                tickerTick(id, width);
                lastTime = timestamp;
            }

            requestAnimationFrame(animationFunc);
        };
        requestAnimationFrame(animationFunc);
    };

    $cms.templates.comcodeJumping = function (params, container) {
        var id = $util.random();

        window.jumperParts[id] = [];
        window.jumperPos[id] = 1;

        for (var i = 0, len = params.parts.length; i < len; i++) {
            window.jumperParts[id].push(params.parts[i].part);
        }

        $dom.html(container, '<span id="' + id + '">' + window.jumperParts[id][0] + '</span>');

        setInterval(function () {
            jumperTick(id);
        }, params.time);
    };

    window.countdown = countdown;
    function countdown(id, direction, tailing) {
        var countdown = (typeof id === 'object') ? id : document.getElementById(id), i;
        var inside = $dom.html(countdown);
        var multiples = [];
        if (tailing >= 4) {
            multiples.push(365);
        }
        if (tailing >= 3) {
            multiples.push(24);
        }
        if (tailing >= 2) {
            multiples.push(60);
        }
        if (tailing >= 1) {
            multiples.push(60);
        }
        multiples.push(1);
        var us = inside.match(/\d+/g);
        var total = 0, multiplier = 1;

        while (multiples.length > us.length) {
            us.push('0');
        }

        for (i = us.length - 1; i >= 0; i--) {
            multiplier *= multiples[i];
            total += parseInt(us[i]) * multiplier;
        }

        if (total > 0) {
            total += Number(direction);
            inside = inside.replace(/\d+/g, '!!!');

            if (total === 0) {
                countdown.classList.add('red-alert');
            }

            for (i = 0; i < us.length; i++) {
                us[i] = Math.floor(total / multiplier);
                total -= us[i] * multiplier;
                multiplier /= multiples[i];
                inside = inside.replace('!!!', us[i]);
            }

            $dom.html(countdown, inside);
        }
    }

    window.tickPos || (window.tickPos = {});

    window.tickerTick = tickerTick;
    function tickerTick(id, width) {
        if (document.hidden === true) {
            return;
        }

        var el = document.getElementById(id);
        if (!el || $dom.$('#' + id + ':hover')) {
            return;
        }

        el.style.textIndent = window.tickPos[id] + 'px';
        window.tickPos[id]--;
        if (window.tickPos[id] < -1.1 * el.children[0].offsetWidth) {
            window.tickPos[id] = width;
        }
    }

    window.jumperPos || (window.jumperPos = []);
    window.jumperParts || (window.jumperParts = []);

    window.jumperTick = jumperTick;
    function jumperTick(id) {
        if (document.hidden === true) {
            return;
        }

        if (window.jumperPos[id] >= (window.jumperParts[id].length)) {
            window.jumperPos[id] = 0;
        }
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        $dom.html(el, window.jumperParts[id][window.jumperPos[id]]);
        window.jumperPos[id]++;
    }

    window.crazyTick = crazyTick;
    function crazyTick() {
        if (window.currentMouseX == null) {
            return;
        }
        if (window.currentMouseY == null) {
            return;
        }

        var e, i, sWidth, biasx, biasy;
        for (i = 0; i < window.crazyCriters.length; i++) {
            e = document.getElementById(window.crazyCriters[i]);
            sWidth = e.clientWidth;

            biasx = window.currentMouseX - e.offsetLeft;
            if (biasx > 0) {
                biasx = 2;
            } else {
                biasx = -1;
            }

            if (Math.random() * 4 < 1) {
                biasx = 0;
            }

            biasy = window.currentMouseY - e.offsetTop;
            if (biasy > 0) {
                biasy = 2;
            } else {
                biasy = -1;
            }

            if (Math.random() * 4 < 1) {
                biasy = 0;
            }

            if (sWidth < 100) {
                e.style.width = (sWidth + 1) + 'px';
            }

            e.style.left = (e.offsetLeft + (Math.random() * 2 - 1 + biasx) * 30) + 'px';
            e.style.top = (e.offsetTop + (Math.random() * 2 - 1 + biasy) * 30) + 'px';
            e.style.position = 'absolute';
        }
    }

    var promiseYouTubeIframeAPIReady;
    $cms.templates.mediaYouTube = function (params, container) {
        // Tie into callback event to see when finished, for our slideshows
        // API: https://developers.google.com/youtube/iframe_api_reference

        if (promiseYouTubeIframeAPIReady == null) {
            promiseYouTubeIframeAPIReady = new Promise(function (resolve) {
                if ((window.YT != null) && (window.YT.Player != null)) {
                    resolve();
                } else {
                    var prevFn = window.onYouTubeIframeAPIReady;
                    window.onYouTubeIframeAPIReady = function onYouTubeIframeAPIReady() {
                        if (typeof prevFn === 'function') {
                            prevFn();
                        }

                        resolve();
                        delete window.onYouTubeIframeAPIReady;
                    };
                    $cms.requireJavascript('https://www.youtube.com/iframe_api');
                }
            });
        }

        promiseYouTubeIframeAPIReady.then(function () {
            /*global YT:false*/
            var embeddedMediaData = $dom.data(container, 'cmsEmbeddedMedia');

            var player = new YT.Player(params.playerId, {
                width: params.width,
                height: params.height,
                videoId: params.remoteId,
                host: 'https://www.youtube-nocookie.com',
                events: {
                    onReady: function () {
                        if (embeddedMediaData != null) {
                            $dom.on(container, 'cms:media:do-play', function () {
                                player.playVideo();
                            });

                            $dom.on(container, 'cms:media:do-pause', function () {
                                player.pauseVideo();
                            });

                            embeddedMediaData.ready = true;
                            $dom.trigger(container, 'cms:media:ready');
                        }
                    },
                    onStateChange: function (newState) {
                        newState = Number(newState.data);

                        if (embeddedMediaData != null) {
                            if (newState === 0/*YT.PlayerState.ENDED*/) {
                                $dom.trigger(container, 'cms:media:ended');
                            } else if (newState === 1/*YT.PlayerState.PLAYING*/) {
                                $dom.trigger(container, 'cms:media:play', {
                                    mediaDuration: player.getDuration(),
                                    mediaCurrentTime: player.getCurrentTime(),
                                });
                            } else if (newState === 2/*YT.PlayerState.PAUSED*/) {
                                $dom.trigger(container, 'cms:media:pause');
                            }
                        }
                    }
                }
            });
        });
    };

    // LEGACY
    $cms.templates.mediaVideoGeneral = function (params, container) {
        // Tie into callback event to see when finished, for our slideshows
        // API: http://developer.apple.com/library/safari/#documentation/QuickTime/Conceptual/QTScripting_JavaScript/bQTScripting_JavaScri_Document/QuickTimeandJavaScri.html
        // API: http://msdn.microsoft.com/en-us/library/windows/desktop/dd563945(v=vs.85).aspx
        $dom.load.then(function () {
            var player = document.getElementById(params.playerId);
            var embeddedMediaData = $dom.data(container, 'cmsEmbeddedMedia');

            if (embeddedMediaData != null) {
                player.addEventListener('playstatechange', function (newState) {
                    if (Number(newState) === 1) {
                        $dom.trigger(container, 'cms:media:ended');
                    }
                });

                player.addEventListener('qt_ended', function () {
                    $dom.trigger(container, 'cms:media:ended');
                });

                $dom.on(container, 'cms:media:do-play', function () {
                    try {
                        player.Play();
                    } catch (e) {}

                    try {
                        player.controls.play();
                    } catch (e) {}
                });

                embeddedMediaData.ready = true;
                $dom.trigger(container, 'cms:media:ready');
            }
        });
    };

    var promiseVimeoApiReady;
    $cms.templates.mediaVimeo = function (params, container) {
        if (promiseVimeoApiReady == null) {
            promiseVimeoApiReady = $cms.requireJavascript('https://player.vimeo.com/api/player.js');
        }

        var playerIframe = document.getElementById(params.playerId),
            embeddedMediaData = $dom.data(container, 'cmsEmbeddedMedia'),
            vimeoPlayer;

        promiseVimeoApiReady.then(function () {
            /*globals Vimeo:false */
            vimeoPlayer = new Vimeo.Player(playerIframe);
            return vimeoPlayer.ready();
        }).then(function () {
            if (embeddedMediaData == null) {
                return;
            }

            vimeoPlayer.on('play', function (data) {
                $dom.trigger(container, 'cms:media:play', {
                    mediaDuration: data.duration,
                    mediaCurrentTime: data.seconds,
                });
            });

            vimeoPlayer.on('pause', function () {
                $dom.trigger(container, 'cms:media:pause');
            });

            vimeoPlayer.on('ended', function () {
                $dom.trigger(container, 'cms:media:ended');
            });

            $dom.on(container, 'cms:media:do-play', function () {
                vimeoPlayer.play();
            });

            $dom.on(container, 'cms:media:do-pause', function () {
                vimeoPlayer.pause();
            });

            embeddedMediaData.ready = true;
            $dom.trigger(container, 'cms:media:ready');
        });
    };

    /* global MediaElementPlayer:false */
    $cms.templates.mediaAudioWebsafe = function mediaAudioWebsafe(params, container) {
        if (typeof MediaElementPlayer !== 'function') {
            $util.error('$cms.templates.mediaAudioWebsafe(): MediaElement.js is not loaded');
            return;
        }

        var playerId = strVal(params.playerId), player,
            width = strVal(params.audio_width),
            height = strVal(params.audio_height),
            url = strVal(params.url),
            options = {
                pluginPath: '{$BASE_URL;}/data/mediaelement/',
                enableKeyboard: true,
                success: function (media) {
                    if (!$cms.configOption('show_inline_stats')) {
                        media.addEventListener('play', function () {
                            $cms.statsEventTrack(null, '{!AUDIO;}', url, null, null, null, true);
                        });
                    }

                    var embeddedMediaData = $dom.data(container, 'cmsEmbeddedMedia');

                    if (embeddedMediaData != null) {
                        media.addEventListener('play', function () {
                            $dom.trigger(container, 'cms:media:play', {
                                mediaDuration: player.duration,
                                mediaCurrentTime: player.currentTime,
                            });
                        });

                        media.addEventListener('canplay', function () {
                            $dom.trigger(container, 'cms:media:canplay');
                        });

                        media.addEventListener('pause', function () {
                            $dom.trigger(container, 'cms:media:pause');
                        });

                        media.addEventListener('ended', function () {
                            $dom.trigger(container, 'cms:media:ended');
                        });

                        $dom.on(container, 'cms:media:do-play', function () {
                            player.play();
                        });

                        $dom.on(container, 'cms:media:do-pause', function () {
                            player.pause();
                        });

                        embeddedMediaData.ready = true;
                        $dom.trigger(container, 'cms:media:ready');
                    }
                }
            };

        // Scale to a maximum width because we can always maximise - for object/embed players we can use max-width for this
        options.videoWidth = Math.min(950, width);
        options.videoHeight = Math.min(height * (950 / width), height);

        player = new MediaElementPlayer(playerId, options);
    };

    $cms.templates.mediaVideoWebsafe = function mediaVideoWebsafe(params, container) {
        if (typeof MediaElementPlayer !== 'function') {
            $util.error('$cms.templates.mediaVideoWebsafe(): MediaElement.js is not loaded');
            return;
        }

        var playerId = strVal(params.playerId),
            mediaElement,
            url = strVal(params.url),
            options = {
                pluginPath: '{$BASE_URL;}/data/mediaelement/',
                enableKeyboard: true,
                success: function (media) {
                    if (document.documentElement.classList.contains('is-gallery-slideshow')) {
                        media.preload = 'auto';
                        media.loop = false;
                    }

                    if (!$cms.configOption('show_inline_stats')) {
                        media.addEventListener('play', function () {
                            $cms.statsEventTrack(null, '{!VIDEO;}', url, null, null, null, true);
                        });
                    }

                    var embeddedMediaData = $dom.data(container, 'cmsEmbeddedMedia');

                    if (embeddedMediaData != null) {
                        media.addEventListener('play', function () {
                            $dom.trigger(container, 'cms:media:play', {
                                mediaDuration: mediaElement.duration,
                                mediaCurrentTime: mediaElement.currentTime,
                            });
                        });

                        media.addEventListener('canplay', function () {
                            $dom.trigger(container, 'cms:media:canplay');
                        });

                        media.addEventListener('pause', function () {
                            $dom.trigger(container, 'cms:media:pause');
                        });

                        media.addEventListener('ended', function () {
                            $dom.trigger(container, 'cms:media:ended');
                        });

                        $dom.on(container, 'cms:media:do-play', function () {
                            mediaElement.play();
                        });

                        $dom.on(container, 'cms:media:do-pause', function () {
                            mediaElement.pause();
                        });

                        $dom.on(container, 'cms:media:do-resize', function () {
                            mediaElement.setPlayerSize();
                            mediaElement.setControlsSize();
                        });

                        embeddedMediaData.ready = true;
                        $dom.trigger(container, 'cms:media:ready');
                    }
                }
            };

        // Scale to a maximum width because we can always maximise - for object/embed players we can use max-width for this
        options.videoWidth = params.playerWidth;
        options.videoHeight = params.playerHeight;

        if (params.responsive) {
            options.stretching = 'responsive';
        }

        mediaElement = new MediaElementPlayer(playerId, options);
    };
}(window.$cms, window.$util, window.$dom));
