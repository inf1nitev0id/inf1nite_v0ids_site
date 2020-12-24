var vm
window.onload = function () {
  vm = new Vue({
    el: '#chart',
    data: {
      day_width: 20,
      lines: lines,
      dates: dates,
			events_list: events,
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
			notSelectedLines: function() {
				var selected = this.selected
				if (selected == 0) {
					return this.lines
				} else {
					return this.lines.filter(function(item) {
						return item.user.id != selected
					})
				}
			},
			selectedLine: function() {
				var selected = this.selected
				if (selected == 0) {
					return []
				} else {
					return this.lines.find(function(item) {
						return item.user.id == selected
					})
				}
			},
      chart: function() {
				var chart = new Array(this.lines.length)
        for (let index = 0; index < this.lines.length; index++) {
          let line = this.lines[index].rating
          let start = false
					let prev = false
          let last_y
					let points = []
          for (let i = 0; i < line.length; i++) {
            if (line[i] !== null || start) {
              if (!start) {
                start = true
              }
              let x, y
              x = Math.floor(i / 2) * this.day_width + this.day_width / 4 + i % 2 * this.day_width / 2
              if (line[i] !== null && line[i] != last_y) {
								y = line[i] * this.scale
	              last_y = line[i]
								points.push({
									x: Math.round(x),
									y: -Math.round(y),
									rate: line[i]
								});
								prev = true
              } else {
								if (prev) {
									y = last_y * this.scale
									points.push({
										x: Math.round(x),
										y: -Math.round(y),
										rate: line[i - 1]
									});
									prev = false
								} else {
									points[points.length - 1].x = x
								}
							}
            }
          }
					chart[index] = points
        }
        return chart;
      },
			points: function() {
				var points = [];
				for (let index = 0; index < this.chart.length; index++) {
					let line = ''
					for (let i = 0; i < this.chart[index].length; i++) {
						line += this.chart[index][i].x + ',' + this.chart[index][i].y + ' '
					}
					points.push(line)
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
              text: length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : ''),
            })
            start_x += length
            month = date.getMonth()
          }
          if (i + 1 == this.dates.length) {
            let length = (i + 1) * this.day_width - start_x
            m.push({
              x: start_x + length / 2,
              text: length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : ''),
            })
          }
        }
        return m
      },
			eventsDays: function() {
				var events = []
				var day = 0
				var same = false;
				for (let i = 0; i < this.events_list.length; i++) {
					while (this.dates[day] < this.events_list[i].date) {
						day++
						same = false
						if (day == this.dates.length) {
							return events
						}
					}
					let event = {
						id: i,
						description: this.events_list[i].description,
						color: this.events_list[i].color,
						important: this.events_list[i].important,
						toString: function() {
							return this.description
						}
					}
					switch (this.events_list[i].type) {
						case 'release':
							event.type = 'R';
							break;
						case 'announcement':
							event.type = 'A';
							break;
						default:
							event.type = 'O';
							break;
					}
					if (same) {
						events[events.length - 1].events.push(event)
					} else {
						events.push({
							date: this.events_list[i].date,
							x: day * this.day_width + this.day_width / 2,
							events: [
								event
							]
						})
						same = true
					}
				}
				return events
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
			isUp: function(y, rate) {
				return -y < String(rate).length * 9
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
			showEvent: function(id) {
				var day = this.events_list[id]
				var message = day.date.getDate() + ' '
					+ this.month_names[day.date.getMonth()].substr(0, 3) + ' '
					+ day.date.getFullYear() + '\n'
					+ day.description
				alert(message)
			}
    },
  })
}
