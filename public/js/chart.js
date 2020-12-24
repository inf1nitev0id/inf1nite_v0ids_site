/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/chart.js":
/*!*******************************!*\
  !*** ./resources/js/chart.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

var vm;

window.onload = function () {
  vm = new Vue({
    el: '#chart',
    data: {
      day_width: 20,
      lines: lines,
      dates: dates,
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
      month_names: ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь']
    },
    created: function created() {
      var _this = this;

      this.dates.forEach(function (item, i) {
        _this.dates[i] = new Date(item);
      });
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
      // ширина графика
      sizeX: function sizeX() {
        return this.dates.length * this.day_width;
      },
      // высота графика
      sizeY: function sizeY() {
        return 600;
      },
      // максимальный рейтинг
      maxRating: function maxRating() {
        var max = 0;

        for (var i = 0; i < this.lines.length; i++) {
          if (this.lines[i].visible && this.lines[i].max > max) max = this.lines[i].max;
        }

        return max;
      },
      // цена вертикального деления
      divisionValue: function divisionValue() {
        var number = this.maxRating;
        var power = 0;
        var result;

        while ((number = Math.floor(number / 10)) >= 10) {
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
      // округлённое максимальное значение рейтинга
      height: function height() {
        return Math.ceil(this.maxRating / this.divisionValue) * this.divisionValue;
      },
      // масштаб рейтинга по отношению к размеру на экране
      scale: function scale() {
        return this.sizeY / this.height;
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
          var line = this.lines[index].rating;
          var start = false;
          var prev = false;
          var last_y = void 0;
          var points = [];

          for (var i = 0; i < line.length; i++) {
            if (line[i] !== null || start) {
              if (!start) {
                start = true;
              }

              var x = void 0,
                  y = void 0;
              x = Math.floor(i / 2) * this.day_width + this.day_width / 4 + i % 2 * this.day_width / 2;

              if (line[i] !== null && line[i] != last_y) {
                y = line[i] * this.scale;
                last_y = line[i];
                points.push({
                  x: Math.round(x),
                  y: -Math.round(y),
                  rate: line[i]
                });
                prev = true;
              } else {
                if (prev) {
                  y = last_y * this.scale;
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

        for (var i = 0; i < this.dates.length; i++) {
          xs.push({
            x: i * this.day_width,
            y: i == 0 ? 0 : this.sizeY + 15 + (this.dates[i].getDate() == 1 ? 15 : 0)
          });
        }

        return xs;
      },
      // массив горизонтальных делений
      horizontalDivisions: function horizontalDivisions() {
        var ys = [];

        for (var y = 0; y < this.height; y += this.divisionValue) {
          ys.push({
            y: Math.round(y * this.scale),
            value: this.height - y
          });
        }

        return ys;
      },
      // массив для шкалы месяцев
      months: function months() {
        var m = [];
        var start_x = 0;
        var month = this.dates[0].getMonth();

        for (var i = 1; i < this.dates.length; i++) {
          var date = this.dates[i];

          if (date.getMonth() != month) {
            var length = i * this.day_width - start_x;
            m.push({
              x: start_x + length / 2,
              text: length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : '')
            });
            start_x += length;
            month = date.getMonth();
          }

          if (i + 1 == this.dates.length) {
            var _length = (i + 1) * this.day_width - start_x;

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
        var same = false;

        for (var i = 0; i < this.events_list.length; i++) {
          var e = this.events_list[i];
          var s_id = e.series_id;

          if (this.types[e.type].visible && (s_id !== null ? this.series[s_id].visible : true) && (!this.important_only || e.important)) {
            while (this.dates[day] < e.date) {
              day++;
              same = false;

              if (day == this.dates.length) {
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
                x: day * this.day_width + this.day_width / 2,
                events: [event]
              });
              same = true;
            }
          }
        }

        return events;
      }
    },
    methods: {
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

/***/ }),

/***/ 3:
/*!*************************************!*\
  !*** multi ./resources/js/chart.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\sites\inf1nite_v0ids_site\resources\js\chart.js */"./resources/js/chart.js");


/***/ })

/******/ });