(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.cmsCalendarRunStart = function cmsCalendarRunStart() {
        var form = document.getElementById('recurrence_pattern').form,
            start = document.getElementById('start'),
            end = document.getElementById('end'),
            startDay = document.getElementById('start_day'),
            startMonth = document.getElementById('start_month'),
            startYear = document.getElementById('start_year'),
            startTime = document.getElementById('start_time'),
            startHour = document.getElementById('start_hour'),
            startMinute = document.getElementById('start_minute'),
            endTime = document.getElementById('end_time'),
            endHour = document.getElementById('end_hour'),
            endMinute = document.getElementById('end_minute'),
            doTimezoneConv = document.getElementById('do_timezone_conv'),
            allDayEvent = document.getElementById('all_day_event');

        updateForm();
        for (var i = 0; i < form.elements['recurrence'].length; i++) {
            form.elements['recurrence'][i].addEventListener('click', updateForm);
        }

        if (startDay) {
            startDay.addEventListener('change', updateForm);
            startMonth.addEventListener('change', updateForm);
            startYear.addEventListener('change', updateForm);
        } else {
            start.addEventListener('change', updateForm);
        }
        if (startHour) {
            startHour.addEventListener('change', updateForm);
            startMinute.addEventListener('change', updateForm);
        } else {
            startTime.addEventListener('change', updateForm);
        }

        updateForm2();
        $dom.on(allDayEvent, 'click', updateForm2);

        form.addEventListener('submit', function (submitEvent) {
            if ((form.elements['end_day'] != null) && (form.elements['end_day'].selectedIndex !== 0) || (form.elements['end'] != null) && (form.elements['end'].value !== '')) {
                var startDate, endDate;
                if (startDay) {
                    startDate = new Date(parseInt(form.elements['startYear'].value), parseInt(form.elements['startMonth'].value) - 1, parseInt(form.elements['startDay'].value), parseInt(form.elements['startHour'].value), parseInt(form.elements['startMinute'].value));
                    endDate = new Date(parseInt(form.elements['end_year'].value), parseInt(form.elements['end_month'].value) - 1, parseInt(form.elements['end_day'].value), parseInt(form.elements['endHour'].value), parseInt(form.elements['endMinute'].value));
                } else {
                    startDate = start.value;
                    endDate = end.value;
                }

                if (startDate > endDate) {
                    submitEvent.preventDefault();
                    $cms.ui.alert('{!calendar:EVENT_CANNOT_AROUND;}');
                    return false;
                }
            }
        });

        function updateForm(event) {
            var s = (form.elements['recurrence'][0].checked);

            if (form.elements['recurrence_pattern']) {
                form.elements['recurrence_pattern'].disabled = s;
            }
            if (form.elements['recurrences']) {
                form.elements['recurrences'].disabled = s;
            }
            if (form.elements['seg_recurrences']) {
                form.elements['seg_recurrences'].disabled = s;
            }

            var hasDateSet = false;
            if (startDay) {
                hasDateSet = (startDay.selectedIndex !== 0) && (startMonth.selectedIndex !== 0) && (startYear.selectedIndex !== 0);
            } else {
                hasDateSet = (start.value !== '');
            }

            if ((event != null) && hasDateSet) { // Something changed
                var url = 'calendar_recurrence_suggest';
                url += '&monthly_spec_type=' + encodeURIComponent($cms.form.radioValue(form.elements['monthly_spec_type']));
                if (startDay) {
                    url += '&date_day=' + encodeURIComponent(startDay.value);
                    url += '&date_month=' + encodeURIComponent(startMonth.value);
                    url += '&date_year=' + encodeURIComponent(startYear.value);
                } else {
                    url += '&date=' + encodeURIComponent(start.value);
                }
                if (startHour) {
                    url += '&date_time_hour=' + encodeURIComponent(startHour.value);
                    url += '&date_time_minute=' + encodeURIComponent(startMinute.value);
                } else {
                    url += '&date_time=' + encodeURIComponent(startTime.value);
                }
                if (doTimezoneConv) {
                    url += '&do_timezone_conv=' + (doTimezoneConv.checked ? '1' : '0');
                }
                url += '&all_day_event=' + (allDayEvent.checked ? '1' : '0');

                $cms.loadSnippet(url).then(function (newData) {
                    var tr = $dom.closest(form.elements['monthly_spec_type'][0], 'tr');
                    $dom.html(tr, newData.replace(/<tr [^>]*>/, '').replace(/<\/tr>/, ''));
                    updateMonthlyRecurrence();
                });
            } else {
                updateMonthlyRecurrence();
            }
        }

        function updateForm2() {
            var s = allDayEvent.checked;
            if (startHour) {
                startHour.disabled = s;
                startMinute.disabled = s;
            } else {
                startTime.disabled = s;
            }
            if (endHour) {
                endHour.disabled = s;
                endMinute.disabled = s;
            } else {
                endTime.disabled = s;
            }
        }

        function updateMonthlyRecurrence() {
            var monthlyRecurrence = form.elements['recurrence'][3].checked;
            for (var i = 0; i < form.elements['monthly_spec_type'].length; i++) {
                form.elements['monthly_spec_type'][i].disabled = !monthlyRecurrence;
            }
        }
    };

    $cms.templates.calendarEventType = function calendarEventType(params, container) {
        $dom.on(container, 'click', '.js-click-toggle-checkbox-event-type', function (e, el) {
            var checkbox = el.querySelector('.js-checkbox-event-type');

            if (e.target !== checkbox) {
                $dom.toggleChecked(checkbox);
            }
        });
    };
}(window.$cms, window.$util, window.$dom));
