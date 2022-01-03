/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./resources/js/edit_rate.js ***!
  \***********************************/
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

var vm;

window.onload = function () {
  vm = new Vue({
    el: '#edit_form',
    data: {
      min_date: min_date,
      users: users,
      rating: rating,
      days: rating.length,
      current_day: rating.length - 1,
      months: ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'наября', 'декабря'],
      time: (new Date().getHours() + 18) % 24 > 12,
      selected_user: null,
      picture_url: "",
      raw_users_from_picture: [],
      char_height: 29,
      raw_json: "",
      raw_users_from_json: [],
      urls: urls
    },
    computed: {
      current_rating: function current_rating() {
        var array = new Array(this.days);
        var previous = this.current_day - 1 >= 0 && this.current_day - 1 < this.days;
        var current = this.current_day >= 0 && this.current_day < this.days;
        var next = this.current_day + 1 >= 0 && this.current_day + 1 < this.days;

        for (var i = 0; i < this.users.length; i++) {
          array[i] = {
            previous: previous ? this.rating[this.current_day - 1][1][i] : null,
            morning: current ? this.rating[this.current_day][0][i] : null,
            evening: current ? this.rating[this.current_day][1][i] : null,
            next: next ? this.rating[this.current_day + 1][0][i] : null
          };
        }

        return array;
      },
      current_date: function current_date() {
        var date = new Date(this.min_date);
        date.setDate(date.getDate() + this.current_day);
        return date;
      },
      formated_date: function formated_date() {
        return this.current_date.getDate() + ' ' + this.months[this.current_date.getMonth()] + ' ' + this.current_date.getFullYear();
      }
    },
    methods: {
      getRate: function getRate(day, time, user_id) {
        if (day >= 0 && day < this.days) {
          return this.rating[day][time][user_id];
        } else {
          return null;
        }
      },
      getUsersFromPicture: function getUsersFromPicture() {
        var _this = this;

        axios.post(this.urls.scan, {
          url: this.picture_url
        }).then(function (response) {
          var json = response.data;
          _this.raw_users_from_picture = [];
          _this.char_height = json.char_height;
          json.users.forEach(function (item) {
            var user = _this.users.find(function (user) {
              return user.id == item.id;
            });

            if (user) {
              if (_this.time) {
                user.new_rate.evening = item.rate;
              } else {
                user.new_rate.morning = item.rate;
              }
            }
          });
          json.unknown_names.forEach(function (item) {
            _this.raw_users_from_picture.push(item);
          });
          _this.picture_url = '';
        });
      },
      writeUserHash: function writeUserHash(index) {
        if (this.selected_user !== null) {
          var item = this.raw_users_from_picture[index];
          this.users[this.selected_user].hashes.push(item.hash);

          if (this.time) {
            this.users[this.selected_user].new_rate.evening = item.rate;
          } else {
            this.users[this.selected_user].new_rate.morning = item.rate;
          }

          this.raw_users_from_picture.splice(index, 1);
          this.selected_user = null;
        }
      },
      getRawJson: function getRawJson() {
        var _this2 = this;

        axios.get(this.urls.tatsu_top).then(function (response) {
          return _this2.raw_json = JSON.stringify(response.data);
        });
      },
      getUsersFromJson: function getUsersFromJson() {
        var _this3 = this;

        var json = JSON.parse(this.raw_json).rankings;
        this.raw_users_from_json = [];
        json.forEach(function (item) {
          var user = _this3.users.find(function (user) {
            return user.discord_id == item.user_id;
          });

          if (user) {
            if (_this3.time) {
              user.new_rate.evening = item.score;
            } else {
              user.new_rate.morning = item.score;
            }
          } else {
            _this3.raw_users_from_json.push({
              rank: item.rank,
              id: item.user_id,
              name: null,
              score: item.score
            });
          }
        });
      },
      getUserData: function getUserData(index) {
        var _this4 = this;

        if (!this.raw_users_from_json[index].name) {
          axios.get("".concat(this.usrl.discord_user, "/").concat(this.raw_users_from_json[index].id)).then(function (response) {
            return _this4.raw_users_from_json[index].name = response.data.username;
          });
        }
      },
      writeUserId: function writeUserId(index) {
        if (this.selected_user !== null) {
          var item = this.raw_users_from_json[index];
          this.users[this.selected_user].discord_id = item.id;
          this.users[this.selected_user].changed_id = true;

          if (this.time) {
            this.users[this.selected_user].new_rate.evening = item.score;
          } else {
            this.users[this.selected_user].new_rate.morning = item.score;
          }

          this.raw_users_from_json.splice(index, 1);
          this.selected_user = null;
        }
      },
      clearInputs: function clearInputs() {
        this.users.forEach(function (user) {
          user.new_rate.morning = null;
          user.new_rate.evening = null;
        });
      },
      exchangeInputs: function exchangeInputs() {
        var temp;

        var _iterator = _createForOfIteratorHelper(this.users),
            _step;

        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            user = _step.value;
            temp = user.new_rate.morning;
            user.new_rate.morning = user.new_rate.evening;
            user.new_rate.evening = temp;
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      },
      saveData: function saveData() {
        var _this5 = this;

        axios.post(this.urls.load, {
          users: this.users,
          date: this.current_date
        }).then(function (response) {
          console.log(response.data);

          _this5.users.forEach(function (user, index) {
            user.hashes = [];

            if (user.new_rate.morning !== null) {
              _this5.rating[_this5.current_day][0][index] = user.new_rate.morning;
              user.new_rate.morning = null;
            }

            if (user.new_rate.evening !== null) {
              _this5.rating[_this5.current_day][1][index] = user.new_rate.evening;
              user.new_rate.evening = null;
            }

            user.changed_id = false;
          });

          _this5.current_day++;
          _this5.time = false;
        });
      }
    }
  });
};
/******/ })()
;