/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*******************************!*\
  !*** ./resources/js/chart.js ***!
  \*******************************/
var vm;

window.onload = function () {
  vm = new Vue({
    el: '#chart',
    data: {
      chart_width: 0,
      default_day_width: 20,
      lines: lines,
      min_date: min_date,
      days_full: days,
      start_indent: 0,
      end_indent: 0,
      events_list: events,
      types: [{
        name: 'Релиз (R)',
        color: '#00A387',
        visible: true
      }, {
        name: 'Анонс (A)',
        color: '#60E6CF',
        visible: true
      }, {
        name: 'Другое (O)',
        color: '#888888',
        visible: true
      }],
      series: series,
      important_only: false,
      selected_event: null,
      selected: 0,
      month_names: ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'],
      changes_mode: false,
      day_mode: false
    },
    created: function created() {
      window.addEventListener('resize', this.updateChartWidth);
      this.updateChartWidth();
      this.events_list.forEach(function (item) {
        item.date = new Date(item.date);
      });
      this.series.forEach(function (item) {
        item.color = '#' + item.color;
      });

      for (var i = 0; i < this.lines.length; i++) {
        this.lines[i].max = 0;

        for (var j = 0; j < this.lines[i].rating.length; j++) {
          if (this.lines[i].rating[j] > this.lines[i].max) this.lines[i].max = this.lines[i].rating[j];
        }
      }
    },
    computed: {
      // график изменений
      changes: function changes() {
        var c = [];

        for (var l = 0; l < this.lines.length; l++) {
          var line = this.lines[l].rating;
          var changes = {
            id: l,
            rating: []
          };

          for (var i = 0; i < this.days_full * 2; i += 2) {
            if (i > 0 && line[i - 1] !== null) {
              changes.rating.push(line[i] - line[i - 1]);
            } else {
              changes.rating.push(null);
            }

            if (line[i] !== null) {
              changes.rating.push(line[i + 1] - line[i]);
            } else {
              changes.rating.push(line[i + 1]);
            }
          }

          c.push(changes);
        }

        return c;
      },
      // количество дней между выбранными датами
      days: function days() {
        return this.days_full - this.start_indent - this.end_indent;
      },
      // начальная дата с учётом отступа
      startDate: function startDate() {
        var date = new Date(this.min_date);
        date.setDate(date.getDate() + this.start_indent);
        return date;
      },
      // ширина графика
      sizeX: function sizeX() {
        return this.days * this.dayWidth;
      },
      // высота графика
      sizeY: function sizeY() {
        return 600;
      },
      // количество дней стандартной ширины, помещабщихся в видимой части графика
      visibleDays: function visibleDays() {
        return this.chart_width / this.default_day_width;
      },
      // ширина дня
      dayWidth: function dayWidth() {
        return this.visibleDays > this.days ? this.chart_width / this.days : this.default_day_width;
      },
      // массив минимальных и максимальных значений рейтинга по дням
      daysExt: function daysExt() {
        var _this = this;

        var d = [];
        var lines = this.changes_mode ? this.changes.filter(function (item) {
          return _this.lines[item.id].visible;
        }) : this.lines.filter(function (item) {
          return item.visible;
        });
        var is_empty = lines.length == 0;

        for (var _day = 0; _day < this.days_full * 2; _day += 2) {
          if (is_empty) {
            d.push({
              min: 0,
              max: 0
            });
          } else {
            var min = null;
            var max = null;

            for (var i = 0; i < lines.length; i++) {
              var min_rate = this.changes_mode && this.day_mode ? lines[i].rating[_day] + lines[i].rating[_day + 1] : this.min(lines[i].rating[_day], lines[i].rating[_day + 1]);
              var max_rate = this.changes_mode && this.day_mode ? lines[i].rating[_day] + lines[i].rating[_day + 1] : this.max(lines[i].rating[_day], lines[i].rating[_day + 1]);
              if (min === null || min_rate < min && min_rate !== null) min = min_rate;
              if (max === null || max_rate > max && max_rate !== null) max = max_rate;
            }

            d.push({
              min: min,
              max: max
            });
          }
        }

        return d;
      },
      // минимальный рейтинг
      minRating: function minRating() {
        var min = null;

        for (var i = 0; i < this.days; i++) {
          if (min === null || this.daysExt[i + this.start_indent].min < min) min = this.daysExt[i + this.start_indent].min;
        }

        return min;
      },
      // максимальный рейтинг
      maxRating: function maxRating() {
        var max = null;

        for (var i = 0; i < this.days; i++) {
          if (max === null || this.daysExt[i + this.start_indent].max > max) max = this.daysExt[i + this.start_indent].max;
        }

        return max;
      },
      // цена вертикального деления
      divisionValue: function divisionValue() {
        var number = this.maxRating - this.minRating;
        var power = 0;
        var result;

        while ((number = number / 10) >= 10) {
          power++;
        }

        if (number > 5) {
          result = 10;
        } else if (number > 2) {
          result = 5;
        } else if (number > 1) {
          result = 2;
        } else {
          result = 1;
        }

        return result * Math.pow(10, power);
      },
      // минимальное значение рейтинга с учётом цены деления
      bottom: function bottom() {
        return Math.floor(this.minRating / this.divisionValue) * this.divisionValue;
      },
      // максимальное значение рейтинга с учётом цены деления
      top: function top() {
        return Math.ceil(this.maxRating / this.divisionValue) * this.divisionValue;
      },
      // масштаб рейтинга по отношению к размеру на экране
      scale: function scale() {
        return this.sizeY / (this.top - this.bottom);
      },
      // выбранный график
      selectedLine: function selectedLine() {
        var selected = this.selected;

        if (selected == 0) {
          return [];
        } else {
          return this.lines.find(function (item) {
            return item.user.id == selected;
          });
        }
      },
      // массив с расчитанными координатами для графиков
      chart: function chart() {
        var chart = new Array(this.lines.length);

        for (var index = 0; index < this.lines.length; index++) {
          var line = this.changes_mode ? this.changes[index].rating : this.lines[index].rating;
          var prev = false;
          var last_y = void 0;
          var points = [];
          var is_start = this.start_indent == 0;
          var is_end = this.end_indent == 0;

          for (var i = 0 + (this.start_indent - !is_start) * 2; i < line.length - (this.end_indent - !is_end) * 2; i++) {
            if (this.changes_mode && this.day_mode) i++;
            var rate = this.changes_mode && this.day_mode ? line[i] + line[i - 1] : line[i];

            if (rate !== null) {
              var x = void 0,
                  y = void 0;
              x = Math.floor((i - this.start_indent * 2) / 2) * this.dayWidth + (this.changes_mode ? i % 2 * this.dayWidth / 2 : this.dayWidth / 4 + i % 2 * this.dayWidth / 2);

              if (rate != last_y) {
                y = (rate - this.bottom) * this.scale;
                last_y = rate;
                points.push({
                  x: Math.round(x),
                  y: -Math.round(y),
                  rate: rate
                });
                prev = true;
              } else {
                if (prev) {
                  y = (last_y - this.bottom) * this.scale;
                  points.push({
                    x: Math.round(x),
                    y: -Math.round(y),
                    rate: line[i - 1]
                  });
                  prev = false;
                } else {
                  points[points.length - 1].x = x;
                }
              }
            }
          }

          chart[index] = points;
        }

        return chart;
      },
      // массив строк, составленных из координат
      points: function points() {
        var points = [];

        for (var index = 0; index < this.chart.length; index++) {
          var line = '';

          for (var i = 0; i < this.chart[index].length; i++) {
            line += this.chart[index][i].x + ',' + this.chart[index][i].y + ' ';
          }

          points.push(line);
        }

        return points;
      },
      // массив ветикальных делений
      verticalDivisions: function verticalDivisions() {
        var xs = [];
        var date = new Date(this.startDate);

        for (var i = 0; i < this.days; i++) {
          xs.push({
            x: i * this.dayWidth,
            y: i == 0 ? 0 : this.sizeY + 15 + (date.getDate() == 1 ? 15 : 0)
          });
          date.setDate(date.getDate() + 1);
        }

        return xs;
      },
      // массив горизонтальных делений
      horizontalDivisions: function horizontalDivisions() {
        var ys = [];

        for (var y = this.top; y > this.bottom; y -= this.divisionValue) {
          ys.push({
            y: Math.round((this.top - y) * this.scale),
            value: y
          });
        }

        return ys;
      },
      // массив для шкалы дат
      dates: function dates() {
        var d = [];
        var date = new Date(this.startDate);

        for (var i = 0; i < this.days; i++) {
          d.push(date.getDate());
          date.setDate(date.getDate() + 1);
        }

        return d;
      },
      // массив для шкалы месяцев
      months: function months() {
        var m = [];
        var start_x = 0;
        var date = new Date(this.startDate);
        var month = date.getMonth();

        for (var i = 1; i < this.days; i++) {
          date.setDate(date.getDate() + 1);

          if (date.getMonth() != month) {
            var length = i * this.dayWidth - start_x;
            m.push({
              x: start_x + length / 2,
              text: length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : '')
            });
            start_x += length;
            month = date.getMonth();
          }

          if (i + 1 == this.days) {
            var _length = (i + 1) * this.dayWidth - start_x;

            m.push({
              x: start_x + _length / 2,
              text: _length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (_length >= 100 ? ' ' + date.getFullYear() : '')
            });
          }
        }

        return m;
      },
      // массив событий, разбитых по дням
      eventsDays: function eventsDays() {
        var events = [];
        var day = 0;
        var date = new Date(this.startDate);
        var same = false;

        for (var i = 0; i < this.events_list.length; i++) {
          var e = this.events_list[i];
          var s_id = e.series_id;

          if (date <= e.date && this.types[e.type].visible && (s_id !== null ? this.series[s_id].visible : true) && (!this.important_only || e.important)) {
            while (date < e.date) {
              day++;
              date.setDate(date.getDate() + 1);
              same = false;

              if (date > this.end_date) {
                return events;
              }
            }

            var event = {
              id: i,
              name: (s_id !== null ? this.series[s_id].name + ': ' : '') + e.name,
              color: s_id !== null ? this.series[s_id].color : this.types[e.type].color,
              important: e.important,
              type: ['R', 'A', 'O'][e.type]
            };

            if (same) {
              events[events.length - 1].events.push(event);
            } else {
              events.push({
                date: e.date,
                x: day * this.dayWidth + this.dayWidth / 2,
                events: [event]
              });
              same = true;
            }
          }
        }

        return events;
      },
      // массив дат для выбора диапазона
      dates_full: function dates_full() {
        var d = [];
        var date = new Date(this.min_date);

        for (var i = 0; i < this.days_full; i++) {
          d.push({
            id: i,
            full: this.dateToString(date)
          });
          date.setDate(date.getDate() + 1);
        }

        return d;
      }
    },
    methods: {
      // обновление размера графика при изменении размера окна
      updateChartWidth: function updateChartWidth() {
        this.chart_width = document.getElementById('page-title').offsetWidth - 2;
      },
      // возвращает меньшую переменную (в отличие от Math.min игнорирует null)
      min: function min(a, b) {
        if (a === null) {
          return b;
        } else if (b === null) {
          return a;
        } else if (a < b) {
          return a;
        } else {
          return b;
        }
      },
      // возвращает бальшую переменную (в отличие от Math.max игнорирует null)
      max: function max(a, b) {
        if (a === null) {
          return b;
        } else if (b === null) {
          return a;
        } else if (a > b) {
          return a;
        } else {
          return b;
        }
      },
      // установка активного графика
      setSelected: function setSelected(id) {
        if (this.selected == id) {
          this.selected = 0;
        } else {
          this.selected = id;
        }
      },
      // проверка, находится ли надпись на достаточном расстоянии от низа графика
      isUp: function isUp(y, rate) {
        return -y < String(rate).length * 9;
      },
      // отображение всех графиков
      showAll: function showAll() {
        for (var i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = true;
        }
      },
      // сокрытие всех графиков
      hideAll: function hideAll() {
        for (var i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = false;
        }
      },
      // инвертирование видимости графиков
      invert: function invert() {
        for (var i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = !this.lines[i].visible;
        }
      },
      // преобразование объекта даты в строку
      dateToString: function dateToString(date) {
        day = date.getDate();
        month = date.getMonth() + 1;
        return (day < 10 ? '0' + day : day) + '.' + (month < 10 ? '0' + month : month) + '.' + date.getFullYear();
      }
    }
  });
};
/******/ })()
;