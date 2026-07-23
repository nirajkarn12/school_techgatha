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
    if (!y || !m || !d) return null;
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

  function buildEventMap(events) {
    var map = {};
    (events || []).forEach(function (ev) {
      var start = parseAd(ev.event_date);
      if (!start) return;
      var end = parseAd(ev.end_date) || start;
      var cursor = new Date(Date.UTC(start.y, start.m - 1, start.d));
      var last = new Date(Date.UTC(end.y, end.m - 1, end.d));
      if (last < cursor) last = cursor;
      while (cursor <= last) {
        var key = adKeyFromUtcDate(cursor);
        if (!map[key]) map[key] = [];
        map[key].push(ev);
        cursor.setUTCDate(cursor.getUTCDate() + 1);
      }
    });
    return map;
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function SchoolNepaliCalendar(root, options) {
    this.root = root;
    this.events = options.events || [];
    this.eventMap = buildEventMap(this.events);
    this.lang = options.lang || 'en';
    this.labels = options.labels || {};
    this.onSelect = typeof options.onSelect === 'function' ? options.onSelect : function () {};

    var todayKey = adKeyFromLocalDate(new Date());
    var todayBs = toBsFromAdKey(todayKey) || { year: 2082, month: 1, day: 1 };
    this.viewYear = todayBs.year;
    this.viewMonth = todayBs.month;
    this.selectedKey = todayKey;
    this.render();
  }

  SchoolNepaliCalendar.prototype.monthName = function (month) {
    if (this.lang === 'ne' || this.lang === 'hi') return BS_MONTHS_NE[month - 1] || '';
    return BS_MONTHS_EN[month - 1] || '';
  };

  SchoolNepaliCalendar.prototype.shiftMonth = function (delta) {
    var m = this.viewMonth + delta;
    var y = this.viewYear;
    if (m < 1) {
      m = 12;
      y -= 1;
    } else if (m > 12) {
      m = 1;
      y += 1;
    }
    this.viewYear = y;
    this.viewMonth = m;
    this.render();
  };

  SchoolNepaliCalendar.prototype.renderEventsForKey = function (key) {
    var panel = this.root.querySelector('[data-cal-events]');
    if (!panel) return;
    var list = this.eventMap[key] || [];
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

  SchoolNepaliCalendar.prototype.render = function () {
    var self = this;
    var year = this.viewYear;
    var month = this.viewMonth;
    var days = daysInBsMonth(year, month);
    var firstAd = toAdUtc(year, month, 1);
    var startWeekday = firstAd ? firstAd.getUTCDay() : 0;
    var todayKey = adKeyFromLocalDate(new Date());
    var cells = [];
    var i;

    for (i = 0; i < startWeekday; i++) {
      cells.push('<div class="school-cal-day is-empty"></div>');
    }

    for (i = 1; i <= days; i++) {
      var ad = toAdUtc(year, month, i);
      if (!ad) continue;
      var key = adKeyFromUtcDate(ad);
      var hasEvents = !!(this.eventMap[key] && this.eventMap[key].length);
      var isToday = key === todayKey;
      var isSelected = key === this.selectedKey;
      var classes = ['school-cal-day'];
      if (hasEvents) classes.push('has-event');
      if (isToday) classes.push('is-today');
      if (isSelected) classes.push('is-selected');
      cells.push(
        '<button type="button" class="' + classes.join(' ') + '" data-ad="' + key + '" aria-label="' + key + '">' +
          '<span class="school-cal-bs">' + i + '</span>' +
          '<span class="school-cal-ad">' + ad.getUTCDate() + '</span>' +
          (hasEvents ? '<span class="school-cal-dot" aria-hidden="true"></span>' : '') +
        '</button>'
      );
    }

    this.root.innerHTML =
      '<div class="school-cal-shell">' +
        '<div class="school-cal-toolbar">' +
          '<button type="button" class="school-cal-nav" data-cal-prev aria-label="Previous month"><i class="fa fa-chevron-left"></i></button>' +
          '<div class="school-cal-heading">' +
            '<strong>' + this.monthName(month) + ' ' + year + '</strong>' +
            '<span>' + (this.labels.bs_label || 'Bikram Sambat') + '</span>' +
          '</div>' +
          '<button type="button" class="school-cal-nav" data-cal-next aria-label="Next month"><i class="fa fa-chevron-right"></i></button>' +
        '</div>' +
        '<div class="school-cal-weekdays">' +
          WEEKDAYS.map(function (d) { return '<span>' + d + '</span>'; }).join('') +
        '</div>' +
        '<div class="school-cal-grid">' + cells.join('') + '</div>' +
        '<div class="school-cal-legend">' +
          '<span><i class="school-cal-legend-dot"></i> ' + (this.labels.school_event || 'School event') + '</span>' +
          '<span><i class="school-cal-legend-today"></i> ' + (this.labels.today || 'Today') + '</span>' +
        '</div>' +
        '<div class="school-cal-detail">' +
          '<h3 class="school-cal-detail-title" data-cal-selected-label></h3>' +
          '<div data-cal-events></div>' +
        '</div>' +
      '</div>';

    this.root.querySelector('[data-cal-prev]').addEventListener('click', function () {
      self.shiftMonth(-1);
    });
    this.root.querySelector('[data-cal-next]').addEventListener('click', function () {
      self.shiftMonth(1);
    });
    Array.prototype.forEach.call(this.root.querySelectorAll('.school-cal-day[data-ad]'), function (btn) {
      btn.addEventListener('click', function () {
        self.selectedKey = btn.getAttribute('data-ad');
        self.render();
        self.onSelect(self.selectedKey, self.eventMap[self.selectedKey] || []);
      });
    });

    this.renderEventsForKey(this.selectedKey);
  };

  window.SchoolNepaliCalendar = SchoolNepaliCalendar;
})();
