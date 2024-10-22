(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.adminBackupInterfaceCalendar = function adminBackupInterfaceCalendar() {
        var dOb = [
            $dom.$('#schedule_day'),
            $dom.$('#schedule_month'),
            $dom.$('#schedule_year'),
            $dom.$('#schedule_hour'),
            $dom.$('#schedule_minute')
        ];

        var hideFunc;
        if (dOb[0] != null) {
            hideFunc = function () {
                $dom.$('#recurrance_days').disabled = ((dOb[0].selectedIndex + dOb[1].selectedIndex + dOb[2].selectedIndex + dOb[3].selectedIndex + dOb[4].selectedIndex) > 0);
            };

            dOb[0].addEventListener('change', hideFunc);
            dOb[1].addEventListener('change', hideFunc);
            dOb[2].addEventListener('change', hideFunc);
            dOb[3].addEventListener('change', hideFunc);
            dOb[4].addEventListener('change', hideFunc);
        } else {
            dOb = [
                $dom.$('#schedule'),
                $dom.$('#schedule_time')
            ];
            hideFunc = function () {
                $dom.$('#recurrance_days').disabled = ((dOb[0].value !== '') || (dOb[1].value !== ''));
            };
            dOb[0].addEventListener('change', hideFunc);
            dOb[1].addEventListener('change', hideFunc);
        }
        hideFunc();
    };

    $cms.templates.backupLaunchScreen = function backupLaunchScreen() {
        var submitButton = $dom.$('#submit-button'),
            maxSizeField = $dom.$('#max_size');

        if (!submitButton || !maxSizeField) {
            return;
        }

        submitButton.addEventListener('click', function () {
            submitButton.disabled = true;
        });

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-primary btn-sm buttons--calculate';
        /*{+START,SET,icon_calculate}{+START,INCLUDE,ICON}NAME=buttons/calculate{+END}{+END}*/
        $dom.html(button, '{$GET;^,icon_calculate} {!backups:CALCULATE_SIZE;^}');
        submitButton.parentNode.insertBefore(button, submitButton);
        button.addEventListener('click', function () {
            var progressTicker = document.createElement('img');
            progressTicker.src = $util.srl('{$IMG;,loading}');
            progressTicker.width = '20';
            progressTicker.height = '20';
            progressTicker.style.verticalAlign = 'middle';
            progressTicker.style.marginRight = '20px';
            button.parentNode.insertBefore(progressTicker, null);

            $cms.loadSnippet('backup_size&max_size=' + encodeURIComponent(maxSizeField.value)).then(function (size) {
                $cms.ui.alert($util.format('{!backups:CALCULATED_SIZE;^}', [size]));
                button.parentNode.removeChild(progressTicker);
            });
        });
    };
}(window.$cms, window.$util, window.$dom));
