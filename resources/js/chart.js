var vm
window.onload = function () {
	vm = new Vue({
		el: '#chart',
		data: {
			day_width: 20,
			lines: lines,
			dates: dates,
			events_list: events,
			types: [
				{
					name: 'Релиз (R)',
					color: '#00A387',
					visible: true
				},
				{
					name: 'Анонс (A)',
					color: '#60E6CF',
					visible: true
				},
				{
					name: 'Другое (O)',
					color: '#888888',
					visible: true
				}
			],
			series: series,
			important_only: false,
			selected_event: null,
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
			this.dates.forEach((item, i) => {
				this.dates[i] = new Date(item)
			})
			this.events_list.forEach(item => {
				item.date = new Date(item.date)
			})
			this.series.forEach(item => {
				item.color = '#' + item.color
			});

			for (let i = 0; i < this.lines.length; i++) {
				this.lines[i].max = 0
				for (let j = 0; j < this.lines[i].rating.length; j++) {
					if (this.lines[i].rating[j] > this.lines[i].max)
						this.lines[i].max = this.lines[i].rating[j]
				}
			}
		},
		computed: {
// ширина графика
			sizeX: function() {
				return this.dates.length * this.day_width
			},
// высота графика
			sizeY: function() {
				return 600
			},
// максимальный рейтинг
			maxRating: function() {
				var max = 0
				for (let i = 0; i < this.lines.length; i++) {
					if (this.lines[i].visible && this.lines[i].max > max)
						max = this.lines[i].max
				}
				return max
			},
// цена вертикального деления
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
// округлённое максимальное значение рейтинга
			height: function() {
				return Math.ceil(this.maxRating / this.divisionValue) * this.divisionValue
			},
// масштаб рейтинга по отношению к размеру на экране
			scale: function() {
				return this.sizeY / this.height
			},
// выбранный график
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
// массив с расчитанными координатами для графиков
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
// массив строк, составленных из координат
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
// массив ветикальных делений
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
// массив горизонтальных делений
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
// массив для шкалы месяцев
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
// массив событий, разбитых по дням
			eventsDays: function() {
				var events = []
				var day = 0
				var same = false;
				for (let i = 0; i < this.events_list.length; i++) {
					let e = this.events_list[i]
					let s_id = e.series_id
					if (this.types[e.type].visible && (s_id !== null ? this.series[s_id].visible : true) && (!this.important_only || e.important)) {
						while (this.dates[day] < e.date) {
							day++
							same = false
							if (day == this.dates.length) {
								return events
							}
						}
						let event = {
							id: i,
							name: (s_id !== null ? this.series[s_id].name + ': ' : '') + e.name,
							color: s_id !== null ? this.series[s_id].color : this.types[e.type].color,
							important: e.important,
							type: ['R', 'A', 'O'][e.type],
						}
						if (same) {
							events[events.length - 1].events.push(event)
						} else {
							events.push({
								date: e.date,
								x: day * this.day_width + this.day_width / 2,
								events: [
									event
								]
							})
							same = true
						}
					}
				}
				return events
			},
		},
		methods: {
// установка активного графика
			setSelected: function(id) {
				if (this.selected == id) {
					this.selected = 0
				} else {
					this.selected = id
				}
			},
// проверка, находится ли надпись на достаточном расстоянии от низа графика
			isUp: function(y, rate) {
				return -y < String(rate).length * 9
			},
// отображение всех графиков
			showAll: function() {
				for (let i = 0; i < this.lines.length; i++) {
					this.lines[i].visible = true;
				}
			},
// сокрытие всех графиков
			hideAll: function() {
				for (let i = 0; i < this.lines.length; i++) {
					this.lines[i].visible = false;
				}
			},
// инвертирование видимости графиков
			invert: function() {
				for (let i = 0; i < this.lines.length; i++) {
					this.lines[i].visible = !this.lines[i].visible;
				}
			},
// преобразование объекта даты в строку
			dateToString: function(date) {
				day = date.getDate()
				month = date.getMonth() + 1
				return (day < 10 ? '0' + day : day) + '.' + (month < 10 ? '0' + month : month) + '.' + date.getFullYear()
			}
		},
	})
}
