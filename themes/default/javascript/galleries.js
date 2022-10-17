(function ($cms, $util, $dom) {
    'use strict';

    var indexOf = Function.bind.call(Function.call, Array.prototype.indexOf);

    // Implementation for [data-link-start-slideshow]
    // Add to a slideshow link to make it open in a full width iframe
    $cms.behaviors.linkStartSlideshow = {
        attach: function (context) {
            $util.once($dom.$$$(context, '[data-link-start-slideshow]'), 'behavior.linkStartSlideshow').forEach(function (link) {
                if (link.title === '{!LINK_NEW_WINDOW;}') {
                    link.title = '';
                }

                $dom.on(link, 'click', function (e) {
                    e.preventDefault();

                    var isLoading = true;

                    var slideshowUrl = $util.url(link.href);

                    // class="gallery-mosaic-masonry-sizer gallery-slideshow-iframe gallery-slideshow-iframe-wrapper"
                    // ^ Above comment serves to mark the classes as _used_ for the 'css_file' unit test

                    var iframeWrapperEl = $dom.create('div', {
                        className: 'gallery-slideshow-iframe-wrapper has-loading-indicator',
                        onclick: function () {
                            if (isLoading) {
                                iframeWrapperEl.remove();
                                window.removeEventListener('message', postMessageListener);
                            }
                        }
                    });

                    var iframeEl = $dom.create('iframe', {
                        className: 'gallery-slideshow-iframe',
                        css: {
                            display: 'none',
                        },
                        onload: function () {
                            if (isLoading) {
                                isLoading = false;
                                $dom.fadeIn(iframeEl);
                                iframeWrapperEl.classList.remove('has-loading-indicator');
                            }
                        },
                    });

                    iframeEl.src = slideshowUrl;

                    iframeWrapperEl.appendChild(iframeEl);

                    document.body.appendChild(iframeWrapperEl);

                    window.addEventListener('message', postMessageListener);

                    function postMessageListener(e) {
                        if (e.source !== iframeEl.contentWindow) {
                            return;
                        }

                        if (e.data === 'cms-gallery-slideshow-ready') {
                            if (isLoading) {
                                isLoading = false;
                                $dom.fadeIn(iframeEl);
                                iframeWrapperEl.classList.remove('has-loading-indicator');
                            }
                        } else if (e.data === 'cms-gallery-slideshow-exit') {
                            isLoading = false;
                            iframeWrapperEl.remove();
                            window.removeEventListener('message', postMessageListener);
                        }
                    }
                });
            });
        }
    };

    $cms.templates.galleryGridModeScreen = function () {};

    $cms.templates.galleryMosaicModeScreen = function () {};

    $cms.templates.galleryCarouselModeScreen = function (params, container) {
        var glideContainer = container.querySelector('.glide-other-gallery-images'), glide;

        if (glideContainer != null) {
            /* global Glide:false */
            glide = new Glide(glideContainer, {
                perView: 6, // A number of slides visible on the single viewport.
                bound: true, // Stop running `perView` number of slides from the end. Use this option if you don't want to have an empty space after a slider.
                breakpoints: { // Collection of options applied at specified media breakpoints.
                    400: {
                        perView: 2
                    },
                    700: {
                        perView: 3
                    },
                    1300: {
                        perView: 5
                    }
                }
            });
            glide.mount();

            setViewCountClass();
            $dom.on(window, 'resize orientationchange', setViewCountClass);

            var mo = new MutationObserver(setViewCountClass);
            mo.observe(glideContainer, { childList: true, subtree: true });

            $dom.on(glideContainer, 'click', '.btn-glide-prev', function () { // Go to previous slides or the very end
                if (glide.index <= (glide.settings.perView - 1)) {
                    glide.go('>>');
                    return;
                }

                glide.go('=' + Math.max(glide.index - glide.settings.perView, 0));
            });

            $dom.on(glideContainer, 'click', '.btn-glide-next', function () { // Go to next slides or back to the start
                var totalSlides = glideContainer.querySelectorAll('.glide__slide').length;

                if (glide.index >= (totalSlides - glide.settings.perView)) {
                    glide.go('<<');
                    return;
                }

                glide.go('=' + Math.min(glide.index + glide.settings.perView, totalSlides - glide.settings.perView));
            });
        }

        function setViewCountClass() {
            var totalSlides = glideContainer.querySelectorAll('.glide__slide').length;

            glideContainer.classList.toggle('has-single-view', totalSlides <= glide.settings.perView);
            glideContainer.classList.toggle('has-multiple-views', totalSlides > glide.settings.perView);
        }
    };

    $cms.views.BlockMainImageFader = BlockMainImageFader;
    /**
     * @memberof $cms.views
     * @class BlockMainImageFader
     * @extends $cms.View
     */
    function BlockMainImageFader(params) {
        BlockMainImageFader.base(this, 'constructor', arguments);

        var data = {},
            id = strVal(params.randFaderImage),
            milliseconds = Number(params.mill), i;

        this.fpAnimationEl = document.getElementById('image-fader-' + id);
        this.fpAnimationFaderEl = $dom.create('img', { className: 'img-thumb', src: $util.srl('{$IMG;,blank}'), css: { position: 'absolute' }});
        this.teaseTitleEl = document.getElementById('image-fader-title-' + id);
        this.teaseScrollingTextEl = document.getElementById('image-fader-scrolling-text-' + id);

        this.fpAnimationEl.parentNode.insertBefore(this.fpAnimationFaderEl, this.fpAnimationEl);
        this.fpAnimationEl.parentNode.style.position = 'relative';
        this.fpAnimationEl.parentNode.style.display = 'block';

        for (i = 0; i < params.titles.length; i++) {
            this.initializeTitle(data, params.titles[i], i);
        }

        for (i = 0; i < params.html.length; i++) {
            this.initializeHtml(data, params.html[i], i);
        }

        for (i = 0; i < params.images.length; i++) {
            this.initializeImage(data, params.images[i], i, milliseconds, params.images.length);
        }
    }

    $util.inherits(BlockMainImageFader, $cms.View, /**@lends BlockMainImageFader#*/{
        initializeTitle: function (data, value, index) {
            data['title' + index] = value;
            if (index === 0) {
                if (this.teaseTitleEl && data['title' + index]) {
                    $dom.html(this.teaseTitleEl, data['title' + index]);
                }
            }
        },
        initializeHtml: function (data, value, index) {
            data['html' + index] = value;
            if (index === 0) {
                if (this.teaseScrollingTextEl) {
                    $dom.html(this.teaseScrollingTextEl, (data['html' + index] === '') ? '{!MEDIA;^}' : data['html' + index]);
                }
            }
        },
        initializeImage: function (data, value, index, milliseconds, total) {
            var periodInMsecs = 50,
                increment = 3;

            if (periodInMsecs * 100 / increment > milliseconds) {
                periodInMsecs = milliseconds * increment / 100;
                periodInMsecs *= 0.9; // A little give
            }

            data['url' + index] = value;
            new Image().src = data['url' + index]; // precache
            var self = this;
            setTimeout(function () {
                function func() {
                    self.fpAnimationFaderEl.src = self.fpAnimationEl.src;
                    $dom.fadeOut(self.fpAnimationFaderEl);
                    $dom.fadeIn(self.fpAnimationEl);
                    self.fpAnimationEl.src = $util.srl(data['url' + index]);
                    self.fpAnimationFaderEl.style.left = ((self.fpAnimationFaderEl.parentNode.offsetWidth - self.fpAnimationFaderEl.offsetWidth) / 2) + 'px';
                    self.fpAnimationFaderEl.style.top = ((self.fpAnimationFaderEl.parentNode.offsetHeight - self.fpAnimationFaderEl.offsetHeight) / 2) + 'px';
                    if (self.teaseTitleEl && data['title' + index]) {
                        $dom.html(self.teaseTitleEl, data['title' + index]);
                    }
                    if (self.teaseScrollingTextEl) {
                        $dom.html(self.teaseScrollingTextEl, data['html' + index]);
                    }
                }

                if (index !== 0) {
                    func();
                }

                setInterval(func, milliseconds * total);
            }, index * milliseconds);
        }
    });

    $cms.views.GalleryNav = GalleryNav;
    /**
     * @memberof $cms.views
     * @class $cms.views.GalleryNav
     * @extends $cms.View
     */
    function GalleryNav() {
        GalleryNav.base(this, 'constructor', arguments);
    }

    $util.inherits(GalleryNav, $cms.View, /**@lends $cms.views.GalleryNav#*/{
        events: function () {
            return {};
        },
    });

    $cms.views.GallerySlideshowScreen = GallerySlideshowScreen;
    /**
     * @memberof $cms.views
     * @class $cms.views.GallerySlideshowScreen
     * @extends $cms.View
     */
    function GallerySlideshowScreen() {
        GallerySlideshowScreen.base(this, 'constructor', arguments);

        document.documentElement.classList.add('is-gallery-slideshow');

        this.applySettings();

        this.setupButtons();

        this.setupMediaSlider();

        this.setupThumbsCarousel();

        this.setupProgressBar();

        this.setSlideshowMainHeight();
        $dom.on(window, 'resize orientationchange', this.setSlideshowMainHeight.bind(this));

        this.setupMediaWrappersSizing();

        $dom.on(window, 'keydown', $util.throttle(this.onKeyDown.bind(this), 200));

        if (window.parent !== window) {
            window.parent.postMessage('cms-gallery-slideshow-ready');
        }
    }

    $util.inherits(GallerySlideshowScreen, $cms.View, /**@lends $cms.views.GallerySlideshowScreen#*/{
        events: function () {
            return {
                'click .btn-exit-slideshow' : 'exitSlideshow',
                'click .btn-toggle-play': 'togglePlaying',
                'click .btn-toggle-details': 'toggleDetails',
                'click .btn-toggle-fullscreen': 'toggleFullscreen',
                'click .btn-toggle-tab': 'toggleTab',
                'click .slideshow-carousel-entry': 'carouselEntryClick',
                'click .btn-slider-control-prev': 'sliderPrevClick',
                'click .btn-slider-control-next': 'sliderNextClick',

                'change .input-slide-duration': 'updateSlideDuration',
                'change .select-slide-transition-effect': 'updateSlideTransitionEffect',
                'change .checkbox-stretch-small-media': 'updateStretchSmallMedia',
                'change .select-background-color': 'updateBackgroundColor',
            };
        },

        getCurrentSlideEl: function () {
            return this.$('.slideshow-media-box-item.active');
        },

        getCurrentCarouselEntry: function () {
            return this.$('.slideshow-carousel-entry.is-current');
        },

        getCurrentSlideIndex: function () {
            return indexOf(this.thumbsCarouselItemsEl.children, this.getCurrentCarouselEntry());
        },

        getSlideDuration: function () {
            return Number(this.el.dataset.vwSlideDuration);
        },

        getSlideEmbeddedMedia: function () {
            return this.getCurrentSlideEl().querySelector('[data-cms-embedded-media]');
        },

        isCurrentSlideImage: function () {
            return this.getCurrentCarouselEntry().classList.contains('is-image');
        },

        isCurrentSlideVideo: function () {
            return this.getCurrentCarouselEntry().classList.contains('is-video');
        },

        isCurrentSlideLast: function () {
            return this.getCurrentSlideIndex() === (this.thumbsCarouselItemsEl.children.length - 1);
        },

        // Height required to be set because of CSS limitations
        setSlideshowMainHeight: function () {
            var mainEl = this.$('.slideshow-main');

            mainEl.style.maxHeight = mainEl.style.height = '';
            mainEl.style.maxHeight = mainEl.style.height = (mainEl.offsetHeight - this.thumbsCarouselEl.offsetHeight) + 'px';
        },

        updateStatusMessage: function (appendMessage) {
            var currentSlideNum = 1 + this.getCurrentSlideIndex(),
                message = $util.format('{!galleries:VIEWING_SLIDE;^}', [currentSlideNum, this.params.totalItems]);

            if (appendMessage != null) {
                message += ' ' + strVal(appendMessage);
            }

            $dom.html(this.$('.slideshow-status'), message);
        },

        setupButtons: function () {
            if (window !== window.parent) {
                // Inside an iframe, show exit button
                $dom.show(this.$('.btn-exit-slideshow'));
            }

            if (!Element.prototype.requestFullscreen || !document.exitFullscreen) {
                // Full screen API not available, hide button
                $dom.hide(this.$('.btn-toggle-fullscreen'));
            }
        },

        setupMediaSlider: function () {
            this._switchSlidePromise = Promise.resolve();

            this.mediaSliderEl = this.$('.slideshow-media-box');
            this.mediaSlider = new $dom.Slider(this.mediaSliderEl, { interval: 0 });
        },

        setupThumbsCarousel: function () {
            this.thumbsCarouselEl = this.$('.slideshow-carousel.glide');
            this.thumbsCarouselItemsEl = this.thumbsCarouselEl.querySelector('.glide__slides');

            /* global Glide:false */
            this.thumbsCarousel = new Glide(this.thumbsCarouselEl, {
                perView: 12, // A number of slides visible on the single viewport.
                bound: true, // Stop running `perView` number of slides from the end. Use this option if you don't want to have an empty space after a slider.
                keyboard: false, // Disabled. Keyboard navigation is reserved for the slideshow.
                breakpoints: { // Collection of options applied at specified media breakpoints.
                    300: {
                        perView: 3
                    },
                    600: {
                        perView: 4
                    },
                    800: {
                        perView: 6
                    },
                    1200: {
                        perView: 8
                    },
                    1600: {
                        perView: 10
                    }
                }
            });

            this.thumbsCarousel.mount();

            this.updateThumbsCarousel();
        },

        // Makes sure current slide's thumbnail is visible.
        updateThumbsCarousel: function () {
            var currentIndex = this.getCurrentSlideIndex();

            if ((Number(this.thumbsCarousel.index) !== currentIndex) && ((currentIndex < this.thumbsCarousel.index) || ((currentIndex - this.thumbsCarousel.index) >= this.thumbsCarousel.settings.perView))) {
                // `Glide#index` is always the first carousel item currently displayed.
                this.thumbsCarousel.go('=' + currentIndex);
            }
        },

        exitSlideshow: function () {
            window.parent.postMessage('cms-gallery-slideshow-exit');
        },

        togglePlaying: function () {
            if (this.isPlaying()) {
                this.stopPlaying();
            } else {
                this.startPlaying();
            }
        },

        isPlaying: function () {
            return this.el.classList.contains('is-playing');
        },

        _slideDelayStartedAt: null,
        _slideDelayDuration: null,

        _playCounter: 0,

        startPlaying: function () {
            var self = this,
                playCounter = ++this._playCounter; // Needed to check for plays being aborted during async actions

            this.el.classList.add('is-playing');
            this.$('.btn-toggle-play').classList.add('is-active');
            $cms.ui.setIcon(this.$('.btn-toggle-play .icon'), 'buttons/pause');

            if (this.isCurrentSlideImage()) {
                this._slideDelayStartedAt = Date.now();
                this._slideDelayDuration = this.getSlideDuration() * 1000;

                setTimeout(function () {
                    if (playCounter !== self._playCounter) {
                        return; // Abort
                    }

                    if (self.isCurrentSlideLast()) {
                        self.stopPlaying();
                        self.updateStatusMessage('{!galleries:LAST_SLIDE;^}');
                        return;
                    }

                    self.switchToSlide('+=1').then(function () {
                        if (playCounter !== self._playCounter) {
                            return;
                        }

                        self.startPlaying();
                    });
                }, this._slideDelayDuration);
            } else {
                this._slideDelayStartedAt = null;

                this.updateStatusMessage('{!galleries:WILL_CONTINUE_AFTER_VIDEO_FINISHED;^}');

                var embeddedMedia = this.getSlideEmbeddedMedia(),
                    embeddedMediaData = (embeddedMedia != null) ? $dom.data(embeddedMedia, 'cmsEmbeddedMedia') : null;

                (new Promise(function (resolve) {
                    if (embeddedMedia && embeddedMediaData.ready) {
                        resolve();
                    }

                    $dom.one(self.getCurrentSlideEl(), 'cms:media:ready', function (e) {
                        embeddedMedia = e.target;
                        embeddedMediaData = $dom.data(embeddedMedia, 'cmsEmbeddedMedia');
                        resolve();
                    });
                })).then(function () {
                    if (playCounter !== self._playCounter) {
                        return $util.promiseHalt();
                    }

                    var endedPromise = new Promise(function (resolve) {
                        $dom.one(embeddedMedia, 'cms:media:ended', function () {
                            resolve();
                        });
                    });

                    $dom.trigger(embeddedMedia, 'cms:media:do-play');

                    return endedPromise;
                }).then(function () {
                    if (playCounter !== self._playCounter) {
                        return $util.promiseHalt();
                    }

                    if (self.isCurrentSlideLast()) {
                        self.stopPlaying();
                        self.updateStatusMessage('{!galleries:LAST_SLIDE;^}');
                        return $util.promiseHalt();
                    }

                    return self.switchToSlide('+=1');
                }).then(function () {
                    if (playCounter !== self._playCounter) {
                        return $util.promiseHalt();
                    }

                    self.startPlaying();
                });
            }
        },

        stopPlaying: function () {
            this._playCounter += 1;
            this._slideDelayStartedAt = null;

            this.el.classList.remove('is-playing');
            this.$('.btn-toggle-play').classList.remove('is-active');
            $cms.ui.setIcon(this.$('.btn-toggle-play .icon'), 'content_types/multimedia');

            this.updateStatusMessage();

            var slideEl = this.getCurrentSlideEl();
            if (slideEl.classList.contains('is-video') && !slideEl.classList.contains('has-loading-indicator')) {
                var embeddedMedia = this.getSlideEmbeddedMedia(),
                    embeddedMediaData = $dom.data(embeddedMedia, 'cmsEmbeddedMedia');

                if (embeddedMediaData.ready && embeddedMediaData.listens && embeddedMediaData.listens.includes('do-pause')) {
                    $dom.trigger(embeddedMedia, 'cms:media:do-pause');
                }
            }
        },

        setupProgressBar: function () {
            if (this._slideDelayStartedAt == null) {
                this.$('.slideshow-progress-bar-fill').style.removeProperty('width');
            } else {
                var progressPercentage = (Date.now() - this._slideDelayStartedAt) / this._slideDelayDuration;
                this.$('.slideshow-progress-bar-fill').style.width = Math.min((progressPercentage * 100), 100) + '%';
            }
            requestAnimationFrame(this.setupProgressBar.bind(this));
        },

        toggleDetails: function (e, btn) {
            if (this.el.classList.contains('is-enabled-show-details')) {
                this.el.classList.remove('is-enabled-show-details');
                btn.classList.remove('is-active');
            } else {
                if (this.getCurrentSlideEl().querySelector('.slideshow-details-overlay') == null) {
                    // Initial slide lacks the details overlay
                    var detailsOverlay = this.getCurrentCarouselEntry().querySelector('.slideshow-details-overlay').cloneNode(true);
                    detailsOverlay.removeAttribute('hidden');

                    this.getCurrentSlideEl().querySelector('.slideshow-media-wrapper').appendChild(detailsOverlay);
                }

                this.el.classList.add('is-enabled-show-details');
                btn.classList.add('is-active');
            }
        },

        toggleFullscreen: function () {
            if (document.fullscreenElement == null) {
                this.el.requestFullscreen();
                this.el.classList.add('is-enabled-fullscreen');
                $cms.ui.setIcon(this.$('.btn-toggle-fullscreen .icon'), 'buttons/shrink_size');
            } else {
                document.exitFullscreen();
                this.el.classList.remove('is-enabled-fullscreen');
                $cms.ui.setIcon(this.$('.btn-toggle-fullscreen .icon'), 'buttons/full_size');
            }
        },

        toggleTab: function (e, btn) {
            var self = this,
                tab = btn.dataset.vwTab,
                tabEl = this.$('.slideshow-tab[data-vw-tab="' + tab + '"]');

            if ($dom.isDisplayed(tabEl)) {
                btn.classList.remove('is-active');
                $dom.slideSide(tabEl).then(this.sizeMediaWrappers.bind(this));
            } else {
                this.closeActiveTab().then(function () {
                    btn.classList.add('is-active');
                    $dom.slideSide(tabEl).then(self.sizeMediaWrappers.bind(self));

                    if (tabEl.classList.contains('has-loading-indicator') && (tab === 'comments')) {
                        self.loadCommentsTab();
                    }
                });
            }
        },

        closeActiveTab: function () {
            var tabEls = this.$$('.slideshow-tab');

            this.$$('.btn-toggle-tab.is-active').forEach(function (btn) {
                btn.classList.remove('is-active');
            });

            for (var i = 0; i < tabEls.length; i++) {
                if ($dom.isDisplayed(tabEls[i])) {
                    var promise = $dom.slideSide(tabEls[i]);
                    promise.then(this.sizeMediaWrappers.bind(this));

                    return promise;
                }
            }

            return Promise.resolve();
        },

        onKeyDown: function (e) {
            if ((e.target.localName === 'input') || (e.target.localName === 'textarea')) {
                return;
            }

            var self = this;

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.stopPlaying();
                this._switchSlidePromise = this._switchSlidePromise.then(function () {
                    return self.switchToSlide('-=1');
                });
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.stopPlaying();
                this._switchSlidePromise = this._switchSlidePromise.then(function () {
                    return self.switchToSlide('+=1');
                });
            }
        },

        sliderPrevClick: function () {
            var self = this;
            this.stopPlaying();
            this._switchSlidePromise = this._switchSlidePromise.then(function () {
                return self.switchToSlide('-=1');
            });
        },

        sliderNextClick: function () {
            var self = this;
            this.stopPlaying();
            this._switchSlidePromise = this._switchSlidePromise.then(function () {
                return self.switchToSlide('+=1');
            });
        },

        carouselEntryClick: function (e, carouselEntry) {
            e.preventDefault();

            this.stopPlaying();

            if (carouselEntry.classList.contains('is-current')) {
                return;
            }

            var newIndex = indexOf(this.thumbsCarouselItemsEl.children, carouselEntry);

            var self = this;
            this._switchSlidePromise = this._switchSlidePromise.then(function () {
                return self.switchToSlide(newIndex);
            });
        },

        _cachedVideoHtml: null,

        switchToSlide: function (index) {
            var self = this;

            this._cachedVideoHtml || (this._cachedVideoHtml = {});

            var carouselEntry, entryId, newSliderItem, mediaWrapper, smallImg;

            return Promise.resolve().then(function () {
                var prevEntry = self.getCurrentCarouselEntry(),
                    prevIndex = self.getCurrentSlideIndex(),
                    newIndex = Number(index);

                if (index === '+=1') {
                    newIndex = prevIndex + 1;

                    if (newIndex === self.thumbsCarouselItemsEl.children.length) {
                        newIndex = 0;
                    }
                } else if (index === '-=1') {
                    newIndex = prevIndex - 1;

                    if (newIndex === -1) {
                        newIndex = self.thumbsCarouselItemsEl.children.length - 1;
                    }
                }

                carouselEntry = self.thumbsCarouselItemsEl.children[newIndex];
                entryId = carouselEntry.dataset.vwId;

                prevEntry.classList.remove('is-current');
                carouselEntry.classList.add('is-current');

                newSliderItem = $dom.create('div', {
                    className: 'slideshow-media-box-item cms-slider-item has-loading-indicator ' + (carouselEntry.classList.contains('is-image') ? 'is-image' : 'is-video'),
                    dataset: {
                        vwIndex: newIndex,
                    },
                });

                mediaWrapper = $dom.create('div', { className: 'slideshow-media-wrapper' });

                // Initially we just show the thumbnail
                smallImg = $dom.create('img', {
                    className: 'slideshow-img is-thumbnail',
                    src: carouselEntry.querySelector('img.img-thumb').src,
                });

                var detailsOverlay = carouselEntry.querySelector('.slideshow-details-overlay').cloneNode(true);
                detailsOverlay.removeAttribute('hidden');

                mediaWrapper.appendChild(smallImg);

                mediaWrapper.appendChild(detailsOverlay);

                newSliderItem.appendChild(mediaWrapper);

                self.mediaSliderEl.appendChild(newSliderItem);

                if (carouselEntry.classList.contains('is-image')) {
                    var fullImg = new Image();
                    fullImg.className = 'slideshow-img';
                    fullImg.onload = function () {
                        $dom.replaceWith(smallImg, fullImg);
                        newSliderItem.classList.remove('has-loading-indicator');
                        self.sizeMediaWrappers();
                    };
                    fullImg.src = carouselEntry.dataset.vwFullUrl;
                }

                self.updateStatusMessage();

                self.updateThumbsCarousel();

                var commentsTab = self.$('.slideshow-tab[data-vw-tab="comments"]'),
                    commentsTabInner = commentsTab.querySelector('.slideshow-tab-inner');

                commentsTab.classList.add('has-loading-indicator');

                $dom.empty(commentsTabInner);

                if ($dom.isDisplayed(commentsTab)) {
                    self.loadCommentsTab(carouselEntry);
                }

                return (prevIndex < newIndex) ? self.mediaSlider.next() : self.mediaSlider.prev();
            }).then(function () {
                self.$('.slideshow-media-box-item:not(.active)').remove();

                if (carouselEntry.classList.contains('is-video')) {
                    if (showVideo() === false) {
                        var xhrUrl = '{$FIND_SCRIPT_NOHTTP;^,show_gallery_video}?cat=' + encodeURIComponent(carouselEntry.dataset.vwCat) + '&id=' + encodeURIComponent(entryId) + $cms.keep();
                        $cms.doAjaxRequest(xhrUrl).then(function (xhr) {
                            var response = strVal(xhr.responseText);

                            if ((((xhr.status >= 200) && (xhr.status < 300)) || (xhr.status === 304)) && (response !== '')) {
                                self._cachedVideoHtml[entryId] = response;
                                showVideo();
                            }
                        });
                    }
                }

                function showVideo() {
                    if (self._cachedVideoHtml[entryId] == null) {
                        return false;
                    }

                    newSliderItem.classList.remove('has-loading-indicator');

                    mediaWrapper.style.backgroundImage = 'url("' + smallImg.src.replaceAll('"', '%22') + '")';

                    $dom.replaceWith(smallImg, self._cachedVideoHtml[entryId]);

                    self.sizeMediaWrappers();

                    $dom.one(mediaWrapper, 'cms:media:ready', function () {
                        mediaWrapper.style.backgroundImage = '';
                    });

                    return true;
                }
            });
        },

        applySettings: function () {
            this.updateSlideDuration();
            this.updateSlideTransitionEffect();
            this.updateStretchSmallMedia();
            this.updateBackgroundColor();
        },

        updateSlideDuration: function () {
            var prevValue = this.el.dataset.vwSlideDuration,
                newValue = this.$('.input-slide-duration').value;

            if (prevValue == null) {
                this.el.dataset.vwSlideDuration = newValue;
                return;
            }

            if (prevValue !== newValue) {
                this.el.dataset.vwSlideDuration = newValue;

                if (this.el.classList.contains('is-playing')) {
                    this.stopPlaying();
                    this.startPlaying();
                }
            }
        },

        updateSlideTransitionEffect: function () {
            this.$('.slideshow-media-box').classList.toggle('cms-slider-fade', (this.$('.select-slide-transition-effect').value === 'fade'));
        },

        updateStretchSmallMedia: function () {
            var wasEnabled = this.el.classList.contains('is-enabled-stretch-small'),
                nowEnabled = this.$('.checkbox-stretch-small-media').checked;

            if (wasEnabled === nowEnabled) {
                return;
            }

            this.el.classList.toggle('is-enabled-stretch-small', nowEnabled);

            this.sizeMediaWrappers();
        },

        updateBackgroundColor: function () {
            this.el.classList.toggle('is-bg-color-light', (this.$('.select-background-color').value === 'light'));
            this.el.classList.toggle('is-bg-color-dark', (this.$('.select-background-color').value === 'dark'));
        },

        setupMediaWrappersSizing: function () {
            this.sizeMediaWrappers();

            this.mediaSliderEl.addEventListener('load', this.sizeMediaWrappers.bind(this), /*useCapture*/true); // On new image load
            this.mediaSliderEl.addEventListener('cms:slider:slide', this.sizeMediaWrappers.bind(this));
            this.mediaSliderEl.addEventListener('cms:media:ready', this.sizeMediaWrappers.bind(this));

            $dom.on(window, 'resize orientationchange', this.sizeMediaWrappers.bind(this));
        },

        sizeMediaWrappers: function () {
            var mainEl = this.$('.slideshow-main'),
                maxWidth = mainEl.offsetWidth,
                maxHeight = mainEl.offsetHeight,
                containerAspectRatio = maxWidth / maxHeight,
                isEnabledStretchSmall = this.el.classList.contains('is-enabled-stretch-small');

            this.$$('.slideshow-media-box-item').forEach(function (itemEl) {
                var wrapperEl = itemEl.querySelector('.slideshow-media-wrapper'),
                    mediaWidth, mediaHeight, mediaAspectRatio,
                    embeddedMedia, embeddedMediaData;

                if (itemEl.classList.contains('is-image') || itemEl.classList.contains('has-loading-indicator')) {
                    var img = itemEl.querySelector('.slideshow-img');

                    if (!img.naturalWidth || !img.naturalHeight) {
                        // Not loaded yet
                        wrapperEl.style.removeProperty('width');
                        wrapperEl.style.removeProperty('height');
                        return;
                    }

                    mediaWidth = img.naturalWidth;
                    mediaHeight = img.naturalHeight;
                    mediaAspectRatio = mediaWidth / mediaHeight;
                } else if (itemEl.classList.contains('is-video')) {
                    embeddedMedia = wrapperEl.querySelector('[data-cms-embedded-media]');
                    embeddedMediaData = $dom.data(embeddedMedia, 'cmsEmbeddedMedia');

                    if (embeddedMediaData.aspectRatio != null) {
                        mediaAspectRatio = strVal(embeddedMediaData.aspectRatio);

                        if (mediaAspectRatio.includes(':')) {
                            var tmp = mediaAspectRatio.split(':');
                            mediaAspectRatio = tmp[0] / tmp[1];
                        } else {
                            mediaAspectRatio = Number(mediaAspectRatio);
                        }
                    } else if ((embeddedMediaData.width != null) && (embeddedMediaData.height != null)) {
                        mediaWidth = Number(embeddedMediaData.width);
                        mediaHeight = Number(embeddedMediaData.height);
                        mediaAspectRatio = mediaWidth / mediaHeight;
                    } else {
                        mediaAspectRatio = 16 / 9;
                    }
                }

                var newWidth, newHeight;

                if (!isEnabledStretchSmall && (mediaWidth != null) && (mediaHeight != null) && (mediaWidth <= maxWidth) && (mediaHeight <= maxHeight)) {
                    newWidth = mediaWidth + 'px';
                    newHeight = mediaHeight + 'px';
                } else if (mediaAspectRatio >= containerAspectRatio) {
                    newWidth = maxWidth + 'px';
                    newHeight = Math.floor(maxWidth / mediaAspectRatio) + 'px';
                } else {
                    newWidth = Math.floor(maxHeight * mediaAspectRatio) + 'px';
                    newHeight = maxHeight + 'px';
                }

                if ((wrapperEl.style.width !== newWidth) || (wrapperEl.style.height !== newHeight)) {
                    wrapperEl.style.width = newWidth;
                    wrapperEl.style.height = newHeight;

                    if (embeddedMediaData && embeddedMediaData.listens && embeddedMediaData.listens.includes('do-resize')) {
                        $dom.trigger(embeddedMedia, 'cms:media:do-resize');
                    }
                }
            });
        },

        loadCommentsTab: function (carouselEntry) {
            if (carouselEntry == null) {
                carouselEntry = this.getCurrentCarouselEntry();
            }

            var commentsOptions = carouselEntry.dataset.vwCommentsOptions,
                commentsOptionsHash = carouselEntry.dataset.vwCommentsOptionsHash,
                tabEl = this.$('.slideshow-tab[data-vw-tab="comments"]'),
                tabElInner = tabEl.querySelector('.slideshow-tab-inner');

            if (!tabEl.classList.contains('has-loading-indicator')) {
                return;
            }

            if (!commentsOptions) {
                tabEl.classList.remove('has-loading-indicator');
                $dom.html(tabElInner, '{!COMMENTS_DISABLED;^}');
                return;
            }

            var ajaxUrl = '{$FIND_SCRIPT_NOHTTP;,post_comment}?just_get_comments=1&options=' + encodeURIComponent(commentsOptions) + '&hash=' + encodeURIComponent(commentsOptionsHash) + $cms.keep();

            $cms.doAjaxRequest(ajaxUrl).then(function (xhr) {
                if ((xhr.responseText === '') || (xhr.status === 500)) {
                    return;
                }

                if (!carouselEntry.classList.contains('is-current')) {
                    return; // Slide changed during XHR request?
                }

                tabEl.classList.remove('has-loading-indicator');

                $dom.html(tabElInner, xhr.responseText);

                var commentsForm = tabEl.querySelector('form.js-form-comments');

                if (commentsForm != null) {
                    commentsForm.action = $cms.pageUrl(); // AJAX will have mangled URL (as was not running in a page context), this will fix it back.
                }
            });
        },
    });

    $cms.functions.moduleCmsGalleriesCat = function moduleCmsGalleriesCat() {
        var fn = document.getElementById('fullname');
        if (fn) {
            var form = fn.form;
            fn.addEventListener('change', function () {
                if ((form.elements['gallery_name']) && (form.elements['gallery_name'].value === '')) {
                    form.elements['gallery_name'].value = fn.value.toLowerCase().replace(/[^{$URL_CONTENT_REGEXP_JS}]/g, '_').replace(/_+$/, '').substr(0, 80);
                }
            });
        }
    };

    $cms.functions.moduleCmsGalleriesRunStartAddCategory = function moduleCmsGalleriesRunStartAddCategory() {
        var validValue,
            extraChecks = [];
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) {
            var value = form.elements['gallery_name'].value;
            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_gallery&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['gallery_name'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.templates.blockMainGalleryEmbed = function blockMainGalleryEmbed() {

    };

    $cms.templates.blockMainGalleryMosaic = function blockMainGalleryMosaic(params, container) {
        var itemsContainer = container.querySelector('.gallery-mosaic-items');

        if (itemsContainer) {
            var masonrySizer = $dom.create('div', { className: 'gallery-mosaic-masonry-sizer' });

            itemsContainer.appendChild(masonrySizer);

            /* global Masonry:false */
            var masonry = new Masonry(itemsContainer, {
                columnWidth: masonrySizer,
                itemSelector: '.gallery-mosaic-item',
                gutter: 10,
                percentPosition: true,
            });

            itemsContainer.addEventListener('load', function (e) {
                if (e.target.localName === 'img') {
                    masonry.layout();
                }
            }, true);
        }

        var checkboxToggleDetails = container.querySelector('.js-checkbox-toggle-details'),
            savedValue;

        if (checkboxToggleDetails != null) {
            if ($cms.isCssMode('mobile')) {
                try {
                    savedValue = window.localStorage.getItem('block-' + params.blockId + '-is-enabled-show-details');
                } catch (ignore) { } // The user may have their browser configured to deny permission to persist data for the specified origin.

                if (savedValue === '1') {
                    checkboxToggleDetails.checked = true;
                    container.classList.add('is-enabled-show-details');
                }
            }

            $dom.on(checkboxToggleDetails, 'change', function () {
                container.classList.toggle('is-enabled-show-details', checkboxToggleDetails.checked);

                try {
                    window.localStorage.setItem('block-' + params.blockId + '-is-enabled-show-details', checkboxToggleDetails.checked ? '1' : '0');
                } catch (ignore) { }
            });
        }
    };

    $cms.templates.galleryImportScreen = function () {
        var files = document.getElementById('second_files'), i;

        if (!files) {
            return;
        }

        for (i = 0; i < files.options.length; i++) {
            $dom.on(files[i], 'mouseover', function (event) {
                $cms.ui.activateTooltip(this, event, '<img width="500" src="' + $cms.filter.html($util.rel($cms.getBaseUrl())) + '/uploads/galleries/' + encodeURI(this.value) + '" />', 'auto');
            });
            $dom.on(files[i], 'mousemove', function (event) {
                $cms.ui.repositionTooltip(this, event);
            });
            $dom.on(files[i], 'mouseout', function () {
                $cms.ui.deactivateTooltip(this);
            });
        }
    };
}(window.$cms, window.$util, window.$dom));
