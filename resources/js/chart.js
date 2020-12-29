var vm
window.onload = function () {
	vm = new Vue({
		el: '#chart',
		data: {
			chart_width: 0,
			default_day_width: 20,
			lines: lines,
			min_date: min_date,
			max_date: max_date,
			start_indent: 0,
			end_indent: 0,
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
			window.addEventListener('resize', this.updateChartWidth);
			this.updateChartWidth()

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
// количество дней
			days_full: function() {
				return Math.round((this.max_date - this.min_date) / 24 / 60 / 60 / 1000) + 1
			},
// количество дней между выбранными датами
			days: function() {
				return this.days_full - this.start_indent - this.end_indent
			},
// начальная дата с учётом отступа
			startDate: function() {
				var date = new Date(this.min_date)
				date.setDate(date.getDate() + this.start_indent)
				return date
			},
// ширина графика
			sizeX: function() {
				return this.days * this.dayWidth
			},
// высота графика
			sizeY: function() {
				return 600
			},
// количество дней стандартной ширины, помещабщихся в видимой части графика
			visibleDays: function() {
				return this.chart_width / this.default_day_width
			},
// ширина дня
			dayWidth: function() {
				return this.visibleDays > this.days ? (this.chart_width / this.days) : this.default_day_width
			},
// массив минимальных и максимальных значений рейтинга по дням
			daysExt: function() {
				var d = []
				var lines = this.lines.filter(item => item.visible)
				var is_empty = lines.length == 0
				for (let day = 0; day < this.days_full * 2; day += 2) {
					if (is_empty) {
						d.push({min: 0, max: 0})
					} else {
						let min = null
						let max = null
						for (let i = 0; i < lines.length; i++) {
							let min_rate = this.min(lines[i].rating[day], lines[i].rating[day + 1])
							let max_rate = this.max(lines[i].rating[day], lines[i].rating[day + 1])
							if (min === null || min_rate < min && min_rate !== null)
								min = min_rate
							if (max === null || max_rate > max && max_rate !== null)
								max = max_rate
						}
						d.push({min: min, max: max})
					}
				}
				return d
			},
// минимальный рейтинг
			minRating: function() {
				var min = null
				for (let i = 0; i < this.days; i++) {
					if (min === null || this.daysExt[i + this.start_indent].min < min)
						min = this.daysExt[i + this.start_indent].min
				}
				return min
			},
// максимальный рейтинг
			maxRating: function() {
				var max = null
				for (let i = 1; i < this.days; i++) {
					if (max === null || this.daysExt[i + this.start_indent].max > max)
						max = this.daysExt[i + this.start_indent].max
				}
				return max
			},
// цена вертикального деления
			divisionValue: function() {
				var number = this.maxRating - this.minRating
				var power = 0
				var result
				while ((number = number / 10) >= 10) {
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
// минимальное значение рейтинга с учётом цены деления
			bottom: function() {
				return Math.floor(this.minRating / this.divisionValue) * this.divisionValue
			},
// максимальное значение рейтинга с учётом цены деления
			top: function() {
				return Math.ceil(this.maxRating / this.divisionValue) * this.divisionValue
			},
// масштаб рейтинга по отношению к размеру на экране
			scale: function() {
				return this.sizeY / (this.top - this.bottom)
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
					let prev = false
					let last_y
					let points = []
					let is_start = this.start_indent == 0
					let is_end = this.end_indent == 0
					for (let i = 0 + (this.start_indent - !is_start) * 2; i < line.length - (this.end_indent - !is_end) * 2; i++) {
						if (line[i] !== null) {
							let x, y
							x = Math.floor((i - this.start_indent * 2) / 2) * this.dayWidth + this.dayWidth / 4 + i % 2 * this.dayWidth / 2
							if (line[i] != last_y) {
								y = (line[i] - this.bottom) * this.scale
								last_y = line[i]
								points.push({
									x: Math.round(x),
									y: -Math.round(y),
									rate: line[i]
								});
								prev = true
							} else {
								if (prev) {
									y = (last_y - this.bottom) * this.scale
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
				var date = new Date(this.startDate)
				for (let i = 0; i < this.days; i++) {
					xs.push({
						x: i * this.dayWidth,
						y: i == 0 ? 0 : (this.sizeY + 15 + (date.getDate() == 1 ? 15 : 0)),
					})
					date.setDate(date.getDate() + 1)
				}
				return xs
			},
// массив горизонтальных делений
			horizontalDivisions: function() {
				var ys = []
				for (let y = this.top; y > this.bottom; y -= this.divisionValue) {
					ys.push({
						y: Math.round((this.top - y) * this.scale),
						value: y,
					})
				}
				return ys
			},
// массив для шкалы дат
			dates: function() {
				var d = []
				var date = new Date(this.startDate)
				for (let i = 0; i < this.days; i++) {
					d.push(date.getDate())
					date.setDate(date.getDate() + 1)
				}
				return d
			},
// массив для шкалы месяцев
			months: function() {
				var m = []
				var start_x = 0
				var date = new Date(this.startDate)
				var month = date.getMonth()
				for (let i = 1; i < this.days; i++) {
					date.setDate(date.getDate() + 1)
					if (date.getMonth() != month) {
						let length = i * this.dayWidth - start_x
						m.push({
							x: start_x + length / 2,
							text: length < 60 ? this.month_names[month].substr(0, 3) : this.month_names[month] + (length >= 100 ? ' ' + date.getFullYear() : ''),
						})
						start_x += length
						month = date.getMonth()
					}
					if (i + 1 == this.days) {
						let length = (i + 1) * this.dayWidth - start_x
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
				var date = new Date(this.startDate)
				var same = false;
				for (let i = 0; i < this.events_list.length; i++) {
					let e = this.events_list[i]
					let s_id = e.series_id
					if (date <= e.date && this.types[e.type].visible && (s_id !== null ? this.series[s_id].visible : true) && (!this.important_only || e.important)) {
						while (date < e.date) {
							day++
							date.setDate(date.getDate() + 1)
							same = false
							if (date > this.end_date) {
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
								x: day * this.dayWidth + this.dayWidth / 2,
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
// массив дат для выбора диапазона
			dates_full: function() {
				var d = []
				var date = new Date(this.min_date)
				for (let i = 0; i < this.days_full; i++) {
					d.push({
						id: i,
						full: this.dateToString(date)
					})
					date.setDate(date.getDate() + 1)
				}
				return d
			},
		},
		methods: {
// обновление размера графика при изменении размера окна
			updateChartWidth: function() {
				this.chart_width = document.getElementById('page-title').offsetWidth - 2
			},
// возвращает меньшую переменную (в отличие от Math.min игнорирует null)
			min: function(a, b) {
				if (a === null) {
					return b
				} else if (b === null) {
					return a
				} else if (a < b) {
					return a
				} else {
					return b
				}
			},
// возвращает бальшую переменную (в отличие от Math.max игнорирует null)
			max: function(a, b) {
				if (a === null) {
					return b
				} else if (b === null) {
					return a
				} else if (a > b) {
					return a
				} else {
					return b
				}
			},
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
