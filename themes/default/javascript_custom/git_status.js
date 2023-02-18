(function ($cms, $util) {
    'use strict';

    $cms.views.GitStatus = GitStatus;
    /**
     * @memberof $cms.views
     * @class $cms.views.GitStatus
     * @extends $cms.View
     */
    function GitStatus() {
        GitStatus.base(this, 'constructor', arguments);

        this.form = this.$('#git_status_form');
    }

    $util.inherits(GitStatus, $cms.View, /**@lends $cms.views.GitStatus#*/{
        events: function () {
            return {
                'click .js-btn-refresh-with-ignored': 'refreshWithIgnored',
                'click .js-btn-refresh-without-ignored': 'refreshWithoutIgnored',
                'click .js-btn-show-local-shell-paths': 'showLocalShellPaths',
                'click .js-btn-download-local-tar': 'downloadLocalTar',
                'click .js-btn-push': 'push',
                'click .js-btn-delete-local-changes': 'deleteLocalChanges',
                'click .js-git-local-select-all': 'localSelectAll',
                'click .js-git-local-select-none': 'localSelectNone',

                'click .js-btn-show-remote-shell-paths': 'showRemoteShellPaths',
                'click .js-btn-download-remote-tar': 'downloadRemoteTar',
                'click .js-btn-pull': 'pull',
                'click .js-git-remote-select-all': 'remoteSelectAll',
                'click .js-git-remote-select-none': 'remoteSelectNone',

                'click .js-btn-refresh-file-selected': 'refreshFileSelected',
            };
        },

        refreshWithIgnored: function () {
            this.form.elements['git_action'].value = 'include';
        },

        refreshWithoutIgnored: function () {
            this.form.elements['git_action'].value = 'exclude';
        },

        showLocalShellPaths: function () {
            this.showShellPaths('local_select_');
        },

        downloadLocalTar: function () {
            this.form.elements['git_action'].value = 'local_tar';
        },

        push: function () {
            this.form.elements['git_action'].value = 'push';
        },

        deleteLocalChanges: function () {
            $cms.ui.confirm('Are you sure you want to delete the local changes?', function (result) {
                if (result) {
                    this.form.elements['git_action'].value = 'revert';
                    this.form.submit();
                }
            });
        },

        localSelectAll: function () {
            this.selectAllGitFiles('local_select_', true);
        },

        localSelectNone: function () {
            this.selectAllGitFiles('local_select_', false);
        },

        showRemoteShellPaths: function () {
            this.showShellPaths('remote_select_');
        },

        downloadRemoteTar: function () {
            this.form.elements['git_action'].value = 'remote_tar';
        },

        pull: function () {
            this.form.elements['git_action'].value = 'pull';
        },

        remoteSelectAll: function () {
            this.selectAllGitFiles('remote_select_', true);
        },

        remoteSelectNone: function () {
            this.selectAllGitFiles('remote_select_', false);
        },

        refreshFileSelected: function () {
            this.refreshFileSelection();
        },


        selectAllGitFiles: function (stub, select) {
            for (var i = 0; i < this.form.elements.length; i++) {
                if ((this.form.elements[i].localName === 'input') && (this.form.elements[i].name.substring(0, stub.length) === stub) && (!this.form.elements[i].disabled)) {
                    this.form.elements[i].checked = select;
                }
            }

            this.refreshFileSelection();
        },

        refreshFileSelection: function () {
            var hasSelection;

            hasSelection = this._refreshFileSelection('local_select_');
            document.getElementById('button_local_tar').disabled = !hasSelection;
            document.getElementById('button_push').disabled = !hasSelection;
            document.getElementById('button_local_shell_paths').disabled = !hasSelection;
            document.getElementById('button_revert').disabled = !hasSelection;

            hasSelection = this._refreshFileSelection('remote_select_');
            document.getElementById('button_remote_tar').disabled = !hasSelection;
            document.getElementById('button_remote_shell_paths').disabled = !hasSelection;
        },

        _refreshFileSelection: function (stub) {
            for (var i = 0; i < this.form.elements.length; i++) {
                if ((this.form.elements[i].localName === 'input') && (this.form.elements[i].name.substring(0, stub.length) === stub) && (this.form.elements[i].checked) && (!this.form.elements[i].disabled)) {
                    return true;
                }
            }

            return false;
        },

        showShellPaths: function (stub) {
            var notice = '';

            for (var i = 0; i < this.form.elements.length; i++) {
                if ((this.form.elements[i].localName === 'input') && (this.form.elements[i].name.substring(0, stub.length) === stub) && (this.form.elements[i].checked) && (!this.form.elements[i].disabled)) {
                    if (notice !== '') {
                        notice += ' \\\n';
                    }
                    notice += this.form.elements[i].value;
                }
            }

            $cms.ui.alert(notice, null, 'Paths');
        }
    });
}(window.$cms, window.$util));
