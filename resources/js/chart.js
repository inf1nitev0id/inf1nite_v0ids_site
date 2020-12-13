var vm
window.onload = function () {
  vm = new Vue({
    el: '#chart',
    data: {
      day_width: 20,
      lines: lines,
      dates: dates,
      selected: 0,
      month_names: [
        'январь',
        'февраль',
        'март',
        'апрель',
        'май',
        'июнь',
        'июль',
        'август',
        'сентябрь',
        'октябрь',
        'ноябрь',
        'декабрь',
      ],
    },
    created: function() {
      for (let i = 0; i < this.lines.length; i++) {
        this.lines[i].max = 0
        for (let j = 0; j < this.lines[i].rating.length; j++) {
          if (this.lines[i].rating[j] > this.lines[i].max)
            this.lines[i].max = this.lines[i].rating[j]
        }
      }
    },
    computed: {
      sizeX: function() {
        return this.dates.length * this.day_width
      },
      sizeY: function() {
        return 600
      },
      maxRating: function() {
        var max = 0
        for (let i = 0; i < this.lines.length; i++) {
          if (this.lines[i].visible && this.lines[i].max > max)
          max = this.lines[i].max
        }
        return max
      },
      divisionValue: function() {
        var number = this.maxRating
        var power = 0
        var result
        while ((number = Math.floor(number / 10)) >= 10) {
          power++
        }
        if (number > 5) {
          result = 10
        } else if (number > 2) {
          result = 5
        } else if (number > 1) {
          result = 2
        } else {
          result = 1
        }
        return result * Math.pow(10, power)
      },
      height: function() {
        return Math.ceil(this.maxRating / this.divisionValue) * this.divisionValue
      },
      scale: function() {
        return this.sizeY / this.height
      },
      points: function() {
        var points = new Array(this.lines.length)
        for (let index = 0; index < this.lines.length; index++) {
          var line = this.lines[index].rating
          var start = false
          var last_y
          points[index] = []
          for (let i = 0; i < line.length; i++) {
            if (start || line[i] !== null) {
              if (!start) {
                start = true
              }
              let x, y
              x = Math.floor(i / 2) * this.day_width + this.day_width / 4 + i % 2 * this.day_width / 2
              if (line[i] !== null) {
                y = line[i] * this.scale
                last_y = y
              } else {
                y = last_y
              }
              points[index].push(Math.round(x) + ',' + -Math.round(y))
            }
          }
        }
        return points
      },
      verticalDivisions: function() {
        var xs = []
        for (let i = 0; i < this.dates.length; i++) {
          xs.push({
            x: i * this.day_width,
            y: i == 0 ? 0 : (this.sizeY + 15 + (this.dates[i].getDate() == 1 ? 15 : 0)),
          })
        }
        return xs
      },
      horizontalDivisions: function() {
        var ys = []
        for (let y = 0; y < this.height; y += this.divisionValue) {
          ys.push({
            y: Math.round(y * this.scale),
            value: this.height - y,
          })
        }
        return ys
      },
      months: function() {
        var m = []
        var start_x = 0
        var month = this.dates[0].getMonth()
        for (let i = 1; i < this.dates.length; i++) {
          let date = this.dates[i]
          if (date.getMonth() != month) {
            let length = i * this.day_width - start_x
            m.push({
              x: start_x + length / 2,
              text: length < 60 ? this.month_names[month - 1].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : ''),
            })
            start_x += length
            month = date.getMonth()
          }
          if (i + 1 == this.dates.length) {
            let length = (i + 1) * this.day_width - start_x
            m.push({
              x: start_x + length / 2,
              text: length < 60 ? this.month_names[month - 1].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : ''),
            })
          }
        }
        return m
      },
    },
    methods: {
      setSelected: function(id) {
        if (this.selected == id) {
          this.selected = 0
        } else {
          this.selected = id
        }
      },
      showAll: function() {
        for (let i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = true;
        }
      },
      hideAll: function() {
        for (let i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = false;
        }
      },
      invert: function() {
        for (let i = 0; i < this.lines.length; i++) {
          this.lines[i].visible = !this.lines[i].visible;
        }
      },
    },
  })
}
