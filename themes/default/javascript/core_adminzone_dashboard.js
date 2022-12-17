(function ($cms, $util, $dom) {
    'use strict';
    /**
     * @memberof $cms.views
     * @class BlockMainStaffChecklistCustomTask
     * @extends $cms.View
     */
    function BlockMainStaffChecklistCustomTask() {
        BlockMainStaffChecklistCustomTask.base(this, 'constructor', arguments);

        this.iconChecklistStatus = this.$('.js-icon-checklist-status');
    }

    $util.inherits(BlockMainStaffChecklistCustomTask, $cms.View, /**@lends BlockMainStaffChecklistCustomTask#*/{
        events: function () {
            return {
                'click .js-click-confirm-delete': 'confirmDelete',
                'click .js-click-mark-task': 'markTask',
                'keypress .js-keypress-mark-task': 'markTask'
            };
        },

        markTask: function (e) {
            var data = this.el.dataset,
                id = encodeURIComponent(this.params.id);

            // Prevent events on the delete link from triggering this function
            if (this.$closest(e.target, '.js-click-confirm-delete')) {
                return;
            }

            if (data.vwTaskDone === 'checklist_todo') {
                $cms.loadSnippet('checklist_task_manage', 'type=mark_done&id=' + id);
                $cms.ui.setIcon(this.iconChecklistStatus, 'checklist/checklist_done', '{$IMG;,icons/checklist/checklist_done}');
                data.vwTaskDone = 'checklist_done';
            } else {
                $cms.loadSnippet('checklist_task_manage', 'type=mark_undone&id=' + id);
                $cms.ui.setIcon(this.iconChecklistStatus, 'checklist/checklist_todo', '{$IMG;,icons/checklist/checklist_todo}');
                data.vwTaskDone = 'checklist_todo';
            }
        },

        confirmDelete: function () {
            var viewEl = this.el,
                params = this.params,
                message = params.confirmDeleteMessage,
                id = encodeURIComponent(params.id);

            $cms.ui.confirm(message, function (result) {
                if (result) {
                    $cms.loadSnippet('checklist_task_manage', 'type=delete&id=' + id);
                    $dom.hide(viewEl);
                }
            });
        }
    });

    /**
     * @memberof $cms.views
     * @class BlockMainStaffLinks
     * @extends $cms.View
     */
    function BlockMainStaffLinks() {
        BlockMainStaffLinks.base(this, 'constructor', arguments);
    }

    $util.inherits(BlockMainStaffLinks, $cms.View, /**@lends BlockMainStaffLinks#*/{
        events: function () {
            return {
                'click .js-click-staff-block-flip': 'staffBlockFlip',
                'click .js-click-form-submit-headless': 'headlessSubmit'
            };
        },
        staffBlockFlip: function () {
            var rand = this.params.randStaffLinks,
                show = this.$('#staff-links-list-' + rand + '-form'),
                hide = this.$('#staff-links-list-' + rand),
                isHideDisplayed = $dom.isDisplayed(hide);

            $dom.toggleWithAria(show, isHideDisplayed);
            $dom.toggleWithAria(hide, !isHideDisplayed);
        },
        headlessSubmit: function (e, btn) {
            var form = btn.form,
                blockName = $cms.filter.nl(this.params.blockName),
                map = $cms.filter.nl(this.params.map);

            e.preventDefault();

            ajaxFormSubmitAdminHeadless(form, blockName, map).then((function (submitForm) {
                if (submitForm) {
                    form.submit();
                }
            }).bind(this));
        }
    });

    /**
     * @memberof $cms.views
     * @class BlockMainNotes
     * @extends $cms.View
     */
    function BlockMainNotes() {
        BlockMainNotes.base(this, 'constructor', arguments);
        this.formEl = this.$('.js-form-block-main-notes');
    }

    $util.inherits(BlockMainNotes, $cms.View, /**@lends BlockMainNotes#*/{
        events: function () {
            return {
                'click .js-click-headless-submit': 'headlessSubmit',
                'focus .js-focus-textarea-expand': 'textareaExpand',
                'blur .js-blur-textarea-contract': 'textareaContract',
                'mouseover .js-hover-disable-textarea-size-change': 'disableTextareaSizeChange',
                'mouseout .js-hover-disable-textarea-size-change': 'disableTextareaSizeChange'
            };
        },

        headlessSubmit: function (e) {
            var blockName = $cms.filter.nl(this.params.blockName),
                map = $cms.filter.nl(this.params.map);

            e.preventDefault();

            ajaxFormSubmitAdminHeadless(this.formEl, blockName, map).then((function (submitForm) {
                if (submitForm) {
                    this.formEl.submit();
                }
            }).bind(this));
        },

        textareaExpand: function (e, textarea) {
            textarea.rows = '23';
        },

        textareaContract: function (e, textarea) {
            if (!this.formEl.disableSizeChange) {
                textarea.rows = '10';
            }
        },

        disableTextareaSizeChange: function (e) {
            this.formEl.disableSizeChange = (e.type === 'mouseover');
        }
    });

    $cms.views.BlockMainStaffChecklistCustomTask = BlockMainStaffChecklistCustomTask;
    $cms.views.BlockMainStaffLinks = BlockMainStaffLinks;
    $cms.views.BlockMainNotes = BlockMainNotes;

    $cms.templates.blockMainStaffChecklist = function (params, container) {
        var showAllLink = document.getElementById('checklist-show-all-link'),
            hideDoneLink = document.getElementById('checklist-hide-done-link');

        setTaskHiding(true);

        $dom.on(container, 'click', '.js-click-enable-task-hiding', function () {
            setTaskHiding(true);
        });

        $dom.on(container, 'click', '.js-click-disable-task-hiding', function () {
            setTaskHiding(false);
        });

        $dom.on(container, 'click', '.js-save-custom-task', function (e, button) {
            submitCustomTask(button.form);
            e.preventDefault();
        });

        function setTaskHiding(hideEnable) {
            hideEnable = Boolean(hideEnable);

            var checklistRows = document.querySelectorAll('.checklist-row'), icon;

            for (var i = 0; i < checklistRows.length; i++) {
                icon = checklistRows[i].querySelector('.icon');
                if (hideEnable) {
                    if (icon && $cms.isIcon(icon, 'checklist/checklist_done')) {
                        $dom.hide(checklistRows[i]);
                        checklistRows[i].classList.add('task-hidden');
                    } else {
                        checklistRows[i].classList.remove('task-hidden');
                    }
                } else {
                    if (!$dom.isDisplayed(checklistRows[i])) {
                        $dom.fadeIn(checklistRows[i]);
                    }
                    checklistRows[i].classList.remove('task-hidden');
                }
            }

            $dom.toggle(showAllLink, hideEnable);
            $dom.toggle(hideDoneLink, !hideEnable);
        }

        function submitCustomTask(form) {
            $cms.loadSnippet('checklist_task_manage', 'type=add&recur_every=' + encodeURIComponent(form.elements['recur_every'].value) + '&recur_interval=' + encodeURIComponent(form.elements['recur_interval'].value) + '&task_title=' + encodeURIComponent(form.elements['new_task'].value)).then(function (newTask) {
                form.elements['recur_every'].value = '';
                form.elements['recur_interval'].value = '';
                form.elements['new_task'].value = '';

                $dom.append('#custom-tasks-go-here', newTask);
            });
        }
    };

    $cms.templates.blockMainStaffActions = function (params, container) {
        $dom.on(container, 'change', '.js-onchange-submit-form', function (e, el) {
            el.form.submit();
        });
    };

    function ajaxFormSubmitAdminHeadless(form, blockName, map) {
        var post = '';

        if (blockName !== undefined) {
            blockName = strVal(blockName);
            map = strVal(map);

            var comcode = '[block' + map + ']' + blockName + '[/block]';
            post += 'data=' + encodeURIComponent(comcode);
        }

        for (var i = 0; i < form.elements.length; i++) {
            if (!form.elements[i].disabled && form.elements[i].name) {
                post += '&' + form.elements[i].name + '=' + encodeURIComponent($cms.form.cleverFindValue(form, form.elements[i]));
            }
        }

        return new Promise(function (resolve) {
            /* Calls comcode_convert in order for the block to be evaluated - the block will detect a POST and do the save itself */
            $cms.doAjaxRequest($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}' + $cms.keep(true))), null, post).then(function (xhr) {
                if (xhr.responseText && (xhr.responseText !== 'false')) {
                    var result = xhr.responseXML && xhr.responseXML.querySelector('result');

                    if (result) {
                        var xhtml = result.textContent,
                            elementReplace = form;

                        while (elementReplace.className !== 'form-ajax-target') {
                            elementReplace = elementReplace.parentNode;
                            if (!elementReplace) {
                                // Oh dear, target not found
                                resolve(/*submitForm: */true);
                                return;
                            }
                        }

                        $dom.html(elementReplace, xhtml);
                        $cms.ui.alert('{!SUCCESS;^}');
                        resolve(/*submitForm: */false); // We've handled it internally
                        return;
                    }
                }

                resolve(/*submitForm: */true);
            });
        });
    }
}(window.$cms, window.$util, window.$dom));
