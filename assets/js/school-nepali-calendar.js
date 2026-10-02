(function () {
  'use strict';

  var BS_MONTHS_EN = [
    'Baishakh', 'Jestha', 'Ashadh', 'Shrawan', 'Bhadra', 'Ashwin',
    'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'
  ];
  var BS_MONTHS_NE = [
    'बैशाख', 'जेष्ठ', 'असार', 'श्रावण', 'भदौ', 'आश्विन',
    'कार्तिक', 'मंसिर', 'पुष', 'माघ', 'फाल्गुण', 'चैत्र'
  ];
  var WEEKDAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  function api() {
    return window.NepaliDatePickerConverter || null;
  }

  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function adKeyFromParts(y, m, d) {
    return y + '-' + pad(m) + '-' + pad(d);
  }

  function adKeyFromUtcDate(date) {
    return adKeyFromParts(date.getUTCFullYear(), date.getUTCMonth() + 1, date.getUTCDate());
  }

  function adKeyFromLocalDate(date) {
    return adKeyFromParts(date.getFullYear(), date.getMonth() + 1, date.getDate());
  }

  function parseAd(str) {
    var p = String(str || '').split('-');
    if (p.length !== 3) return null;
    var y = Number(p[0]);
    var m = Number(p[1]);
    var d = Number(p[2]);
    if (!Number.isInteger(y) || !Number.isInteger(m) || !Number.isInteger(d) || y < 1 || m < 1 || m > 12 || d < 1 || d > 31) return null;
    var date = new Date(Date.UTC(y, m - 1, d));
    if (date.getUTCFullYear() !== y || date.getUTCMonth() + 1 !== m || date.getUTCDate() !== d) return null;
    return { y: y, m: m, d: d, key: adKeyFromParts(y, m, d) };
  }

  function toBsFromAdKey(adDateKey) {
    var lib = api();
    if (!lib || !lib.adToBs) return null;
    try {
      var result = lib.adToBs(adDateKey + 'T00:00:00Z');
      if (!result) return null;
      if (typeof result === 'string') {
        var parts = result.split('-').map(Number);
        return { year: parts[0], month: parts[1], day: parts[2] };
      }
      return result;
    } catch (e) {
      return null;
    }
  }

  function toAdUtc(bsYear, bsMonth, bsDay) {
    var lib = api();
    if (!lib || !lib.bsToAd) return null;
    try {
      var result = lib.bsToAd(bsYear, bsMonth, bsDay);
      if (!result || !(result instanceof Date) || isNaN(result.getTime())) return null;
      return result;
    } catch (e) {
      return null;
    }
  }

  function daysInBsMonth(year, month) {
    for (var d = 32; d >= 28; d--) {
      if (toAdUtc(year, month, d)) return d;
    }
    return 30;
  }

  function buildEventRanges(events) {
    return (events || []).map(function (event) {
      var start = parseAd(event.event_date);
      if (!start) return null;
      var end = parseAd(event.end_date) || start;
      if (end.key < start.key) end = start;
      return { event: event, start: start.key, end: end.key };
    }).filter(Boolean);
  }

  function eventsForDate(ranges, key) {
    return ranges.filter(function (range) {
      return key >= range.start && key <= range.end;
    }).map(function (range) {
      return range.event;
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function safeEventColor(color) {
    color = String(color || '').trim();
    return /^#[0-9a-fA-F]{6}$/.test(color) ? color : '#e5262f';
  }

  function SchoolNepaliCalendar(root, options) {
    this.root = root;
    this.events = options.events || [];
    this.eventRanges = buildEventRanges(this.events);
    this.lang = options.lang || 'en';
    this.labels = options.labels || {};
    this.monthsToShow = Math.max(1, Math.min(12, parseInt(options.monthsToShow, 10) || 1));
    this.showToolbar = options.showToolbar !== false;
    this.showAllMonths = options.showAllMonths === true;
    this.showDayDetails = options.showDayDetails !== false;
    this.eventsPanel = options.eventsPanel || null;
    this.activeEventsMonth = null;
    this.onSelect = typeof options.onSelect === 'function' ? options.onSelect : function () {};

    var todayKey = adKeyFromLocalDate(new Date());
    var todayBs = toBsFromAdKey(todayKey) || { year: 2082, month: 1, day: 1 };
    this.viewYear = todayBs.year;
    this.viewMonth = this.showAllMonths ? 1 : todayBs.month;
    this.selectedKey = todayKey;
    this.render();
  }

  SchoolNepaliCalendar.prototype.monthName = function (month) {
    if (this.lang === 'ne' || this.lang === 'hi') return BS_MONTHS_NE[month - 1] || '';
    return BS_MONTHS_EN[month - 1] || '';
  };

  SchoolNepaliCalendar.prototype.shiftMonth = function (delta) {
    var monthIndex = (this.viewYear * 12) + this.viewMonth - 1 + (delta * this.monthsToShow);
    this.viewYear = Math.floor(monthIndex / 12);
    this.viewMonth = (monthIndex % 12) + 1;
    this.activeEventsMonth = { year: this.viewYear, month: this.viewMonth };
    this.render();
  };

  SchoolNepaliCalendar.prototype.renderEventsForKey = function (key) {
    var panel = this.root.querySelector('[data-cal-events]');
    if (!panel) return;
    var list = eventsForDate(this.eventRanges, key);
    var title = this.root.querySelector('[data-cal-selected-label]');
    if (title) {
      var bs = toBsFromAdKey(key);
      var text = key + ' AD';
      if (bs) {
        text = this.monthName(bs.month) + ' ' + bs.day + ', ' + bs.year + ' BS · ' + key + ' AD';
      }
      title.textContent = text;
    }
    if (!list.length) {
      panel.innerHTML = '<div class="school-cal-empty">' + (this.labels.no_events_day || 'No school events on this date.') + '</div>';
      return;
    }
    panel.innerHTML = list.map(function (ev) {
      var meta = [];
      if (ev.event_time) meta.push(ev.event_time);
      if (ev.location) meta.push(ev.location);
      return (
        '<article class="school-cal-event-card">' +
          '<h4>' + escapeHtml(ev.title || '') + '</h4>' +
          (meta.length ? '<p class="school-cal-event-meta">' + escapeHtml(meta.join(' · ')) + '</p>' : '') +
          (ev.description ? '<p class="school-cal-event-desc">' + escapeHtml(ev.description) + '</p>' : '') +
        '</article>'
      );
    }).join('');
  };

  SchoolNepaliCalendar.prototype.renderMonthEvents = function (year, month, panel) {
    panel = panel || this.eventsPanel;
    if (!panel) return;
    var heading = panel.querySelector('[data-cal-month-events-title]');
    var list = panel.querySelector('[data-cal-month-events]');
    if (!heading || !list) return;

    heading.textContent = this.monthName(month) + ' ' + year + ' · ' + (this.labels.month_events_title || "Month's Events and Holidays");
    var monthEvents = [];
    var seen = {};
    var days = daysInBsMonth(year, month);
    for (var day = 1; day <= days; day++) {
      var ad = toAdUtc(year, month, day);
      if (!ad) continue;
      var dateKey = adKeyFromUtcDate(ad);
      eventsForDate(this.eventRanges, dateKey).forEach(function (event) {
        var eventKey = event.id ? 'id-' + event.id : String(event.title || '') + '-' + String(event.event_date || '');
        if (seen[eventKey]) return;
        seen[eventKey] = true;
        monthEvents.push({ event: event, day: day, month: month });
      });
    }

    if (!monthEvents.length) {
      list.innerHTML = '<div class="school-cal-month-events-empty">' + escapeHtml(this.labels.no_month_events || 'No calendar events published yet.') + '</div>';
      return;
    }

    monthEvents.sort(function (a, b) {
      return String(a.event.event_date || '').localeCompare(String(b.event.event_date || ''));
    });
    list.innerHTML = monthEvents.map(function (item) {
      var event = item.event;
      var meta = [];
      var color = safeEventColor(event.event_color);
      if (event.event_time) meta.push(event.event_time);
      if (event.location) meta.push(event.location);
      return (
        '<article class="school-cal-month-event">' +
          '<div class="school-cal-month-event-date" style="--event-color:' + color + '"><strong>' + item.day + '</strong><span>' + escapeHtml(this.monthName(item.month).slice(0, 3)) + '</span></div>' +
          '<div class="school-cal-month-event-copy">' +
            '<h3>' + escapeHtml(event.title || '') + '</h3>' +
            (meta.length ? '<p>' + escapeHtml(meta.join(' · ')) + '</p>' : '') +
          '</div>' +
        '</article>'
      );
    }, this).join('');
  };

  SchoolNepaliCalendar.prototype.renderMonth = function (year, month, todayKey) {
    var days = daysInBsMonth(year, month);
    var firstAd = toAdUtc(year, month, 1);
    var startWeekday = firstAd ? firstAd.getUTCDay() : 0;
    var cells = [];
    var i;

    for (i = 0; i < startWeekday; i++) {
      cells.push('<div class="school-cal-day is-empty"></div>');
    }

    for (i = 1; i <= days; i++) {
      var ad = toAdUtc(year, month, i);
      if (!ad) continue;
      var key = adKeyFromUtcDate(ad);
      var events = eventsForDate(this.eventRanges, key);
      var hasEvents = events.length > 0;
      var eventDotColor = hasEvents ? safeEventColor(events[0].event_color) : '#e5262f';
      var isToday = key === todayKey;
      var isSaturday = ad.getUTCDay() === 6;
      var isSelected = key === this.selectedKey;
      var classes = ['school-cal-day'];
      var eventLabels = this.monthsToShow > 1 ? events.slice(0, 2).map(function (event, eventIndex) {
        return '<span class="school-cal-event-label" style="--event-color:' + safeEventColor(event.event_color) + '" title="' + escapeHtml(event.title || '') + '">' + escapeHtml(event.title || '') + '</span>';
      }).join('') : '';

      if (hasEvents) classes.push('has-event');
      if (isSaturday) classes.push('is-saturday');
      if (isToday) classes.push('is-today');
      if (isSelected) classes.push('is-selected');
      cells.push(
        '<button type="button" class="' + classes.join(' ') + '" style="--event-color:' + eventDotColor + '" data-ad="' + key + '" aria-label="' + key + '">' +
          '<span class="school-cal-bs">' + i + '</span>' +
          '<span class="school-cal-ad">' + ad.getUTCDate() + '</span>' +
          (eventLabels ? '<span class="school-cal-event-labels">' + eventLabels + '</span>' : (hasEvents ? '<span class="school-cal-dot" style="background:' + eventDotColor + '" aria-hidden="true"></span>' : '')) +
        '</button>'
      );
    }

    var monthTitle = this.monthName(month) + ' ' + year;
    return (
      '<section class="school-cal-month' + (this.monthsToShow > 1 ? ' is-multi' : '') + '">' +
        (this.monthsToShow > 1 ? '<h3 class="school-cal-month-title">' + monthTitle + '</h3>' : '') +
        '<div class="school-cal-weekdays">' +
          WEEKDAYS.map(function (day) { return '<span>' + day + '</span>'; }).join('') +
        '</div>' +
        '<div class="school-cal-grid">' + cells.join('') + '</div>' +
      '</section>'
    );
  };

  SchoolNepaliCalendar.prototype.render = function () {
    var self = this;
    var todayKey = adKeyFromLocalDate(new Date());
    var monthViews = [];
    var offset;

    for (offset = 0; offset < this.monthsToShow; offset++) {
      var monthIndex = this.viewMonth - 1 + offset;
      var monthYear = this.showAllMonths ? this.viewYear : this.viewYear + Math.floor(monthIndex / 12);
      var month = this.showAllMonths ? offset + 1 : (monthIndex % 12) + 1;
      monthViews.push({ year: monthYear, month: month });
    }

    var renderedMonths = monthViews.map(function (view) {
      var monthCalendar = self.renderMonth(view.year, view.month, todayKey);
      if (!self.showAllMonths) return monthCalendar;
      return (
        '<div class="school-cal-month-row">' +
          '<div class="school-cal-month-row-calendar">' + monthCalendar + '</div>' +
          '<aside class="school-cal-month-events" data-cal-month-panel data-year="' + view.year + '" data-month="' + view.month + '">' +
            '<h3 class="school-cal-month-events-title" data-cal-month-events-title></h3>' +
            '<div class="school-cal-month-event-list" data-cal-month-events></div>' +
          '</aside>' +
        '</div>'
      );
    }).join('');

    var firstMonth = monthViews[0];
    var lastMonth = monthViews[monthViews.length - 1];
    var activeMonth = this.activeEventsMonth;
    if (!activeMonth || !monthViews.some(function (view) {
      return view.year === activeMonth.year && view.month === activeMonth.month;
    })) {
      activeMonth = firstMonth;
      this.activeEventsMonth = activeMonth;
    }
    var rangeTitle = this.monthsToShow > 1
      ? this.monthName(firstMonth.month) + ' ' + firstMonth.year + ' – ' + this.monthName(lastMonth.month) + ' ' + lastMonth.year
      : this.monthName(firstMonth.month) + ' ' + firstMonth.year;

    this.root.innerHTML =
      '<div class="school-cal-shell' + (this.monthsToShow > 1 ? ' is-multi' : '') + '">' +
        (this.showToolbar ? '<div class="school-cal-toolbar">' +
          '<button type="button" class="school-cal-nav" data-cal-prev aria-label="Previous month"><i class="fa fa-chevron-left"></i></button>' +
          '<div class="school-cal-heading">' +
            '<strong>' + rangeTitle + '</strong>' +
            '<span>' + (this.labels.bs_label || 'Bikram Sambat') + '</span>' +
          '</div>' +
          '<button type="button" class="school-cal-nav" data-cal-next aria-label="Next month"><i class="fa fa-chevron-right"></i></button>' +
        '</div>' : '') +
        '<div class="school-cal-months' + (this.showAllMonths ? ' is-year' : (this.monthsToShow > 1 ? ' is-multi' : '')) + '">' +
          renderedMonths +
        '</div>' +
        '<div class="school-cal-legend">' +
          '<span><i class="school-cal-legend-dot"></i> ' + (this.labels.school_event || 'School event') + '</span>' +
          '<span><i class="school-cal-legend-today"></i> ' + (this.labels.today || 'Today') + '</span>' +
        '</div>' +
        (this.showDayDetails ? '<div class="school-cal-detail">' +
          '<h3 class="school-cal-detail-title" data-cal-selected-label></h3>' +
          '<div data-cal-events></div>' +
        '</div>' : '') +
      '</div>';

    var previousButton = this.root.querySelector('[data-cal-prev]');
    var nextButton = this.root.querySelector('[data-cal-next]');
    if (previousButton && nextButton) {
      previousButton.addEventListener('click', function () {
        self.shiftMonth(-1);
      });
      nextButton.addEventListener('click', function () {
        self.shiftMonth(1);
      });
    }
    if (this.showAllMonths) {
      Array.prototype.forEach.call(this.root.querySelectorAll('[data-cal-month-panel]'), function (panel) {
        self.renderMonthEvents(Number(panel.getAttribute('data-year')), Number(panel.getAttribute('data-month')), panel);
      });
    } else {
      this.renderMonthEvents(activeMonth.year, activeMonth.month);
    }
    Array.prototype.forEach.call(this.root.querySelectorAll('.school-cal-day[data-ad]'), function (btn) {
      btn.addEventListener('click', function () {
        self.selectedKey = btn.getAttribute('data-ad');
        if (self.showAllMonths) {
          Array.prototype.forEach.call(self.root.querySelectorAll('.school-cal-day.is-selected'), function (selected) {
            selected.classList.remove('is-selected');
          });
          btn.classList.add('is-selected');
          self.onSelect(self.selectedKey, eventsForDate(self.eventRanges, self.selectedKey));
          return;
        }
        var selectedBs = toBsFromAdKey(self.selectedKey);
        if (selectedBs) {
          self.activeEventsMonth = { year: selectedBs.year, month: selectedBs.month };
        }
        self.render();
        self.onSelect(self.selectedKey, eventsForDate(self.eventRanges, self.selectedKey));
      });
    });

    this.renderEventsForKey(this.selectedKey);
  };

  window.SchoolNepaliCalendar = SchoolNepaliCalendar;
})();
