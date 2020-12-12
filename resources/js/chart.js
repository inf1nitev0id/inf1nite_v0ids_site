var vm
window.onload = function () {
  vm = new Vue({
    el: '#chart',
    data: {
      day_width: 20,
      lines: lines,
      dates: dates,
      selected: 0,
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
        var power = 0;
        var result;
        while ((number = Math.floor(number / 10)) >= 10)
          power++
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
        for (let x = this.day_width; x < this.sizeX; x += this.day_width) {
          xs.push(x)
        }
        return xs
      },
      horizontalDivisions: function() {
        var ys = []
        for (let y = 0; y < this.height; y += this.divisionValue) {
          ys.push(Math.round(y * this.scale))
        }
        return ys
      },
    },
  })
}
