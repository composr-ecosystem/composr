(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.BlockMainSearch = BlockMainSearch;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function BlockMainSearch() {
        BlockMainSearch.base(this, 'constructor', arguments);
    }

    $util.inherits(BlockMainSearch, $cms.View, /**@lends BlockMainSearch#*/{
        events: function () {
            return {
                'click .js-main-search': 'submitMainSearch',
                'keyup .js-keyup-update-ajax-search-list-with-type': 'updateAjaxSearchListWithType',
                'keyup .js-keyup-update-ajax-search-list': 'updateAjaxSearchList'
            };
        },

        submitMainSearch: function (e, form) {
            if ((form.elements.content == null) || $cms.form.checkFieldForBlankness(form.elements.content)) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        },

        updateAjaxSearchListWithType: function (e, input) {
            $cms.form.updateAjaxSearchList(input, e, this.params.searchType);
        },

        updateAjaxSearchList: function (e, input) {
            $cms.form.updateAjaxSearchList(input, e);
        }
    });

    $cms.views.SearchFormScreen = SearchFormScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function SearchFormScreen() {
        SearchFormScreen.base(this, 'constructor', arguments);

        this.searchFormEl = this.$('.js-search-form');
    }

    $util.inherits(SearchFormScreen, $cms.View, /**@lends SearchFormScreen#*/{
        events: function () {
            return {
                'keyup .js-keyup-update-ajax-search-list': 'updateAjaxSearchList',
                'keyup .js-keyup-update-author-list': 'updateAuthorList',
                'click .js-click-trigger-resize': 'triggerResize'
            };
        },
        updateAjaxSearchList: function (e, input) {
            var params = this.params;

            if (params.searchType !== undefined) {
                $cms.form.updateAjaxSearchList(input, e, $cms.filter.nl(params.searchType));
            } else {
                $cms.form.updateAjaxSearchList(input, e);
            }
        },
        updateAuthorList: function (e, target) {
            $cms.form.updateAjaxMemberList(target, 'author', false, e);
        },
        triggerResize: function () {
            $dom.triggerResize();
        }
    });

    $cms.templates.blockTopSearch = function (params, container) {
        var searchType = $cms.filter.nl(params.searchType);

        $dom.on(container, 'click', 'button', function (e, form) {
            if (form.elements.content === undefined) {
                // Succeed (no search)
                $cms.ui.disableFormButtons(form);
                return;
            }

            if ($cms.form.checkFieldForBlankness(form.elements.content)) {
                // Succeed
                $cms.ui.disableFormButtons(form);
                return;
            }

            // Fail
            e.preventDefault();
        });

        $dom.on(container, 'keyup', '.js-input-keyup-update-ajax-search-list', function (e, input) {
            $cms.form.updateAjaxSearchList(input, e, searchType);
        });
    };
}(window.$cms, window.$util, window.$dom));
