var vm
window.onload = function () {
    vm = new Vue({
        el: '#edit_form',
        data: {
            min_date: min_date,
            users: users,
            rating: rating,
            days: rating.length,
            current_day: rating.length - 1,
            months: [
                'января',
                'февраля',
                'марта',
                'апреля',
                'мая',
                'июня',
                'июля',
                'августа',
                'сентября',
                'октября',
                'наября',
                'декабря',
            ],
            time: ((new Date()).getHours() + 18) % 24 > 12,
            selected_user: null,
            picture_url: "",
            raw_users_from_picture: [],
            char_height: 29,
            raw_json: "",
            raw_users_from_json: [],
            urls: urls,
        },
        computed: {
            current_rating: function () {
                let array = new Array(this.days)
                let previous = this.current_day - 1 >= 0 && this.current_day - 1 < this.days
                let current = this.current_day >= 0 && this.current_day < this.days
                let next = this.current_day + 1 >= 0 && this.current_day + 1 < this.days
                for (let i = 0; i < this.users.length; i++) {
                    array[i] = {
                        previous: previous ? this.rating[this.current_day - 1][1][i] : null,
                        morning: current ? this.rating[this.current_day][0][i] : null,
                        evening: current ? this.rating[this.current_day][1][i] : null,
                        next: next ? this.rating[this.current_day + 1][0][i] : null,
                    }
                }
                return array
            },
            current_date: function () {
                let date = new Date(this.min_date)
                date.setDate(date.getDate() + this.current_day)
                return date
            },
            formated_date: function () {
                return this.current_date.getDate() + ' ' + this.months[this.current_date.getMonth()] + ' ' + this.current_date.getFullYear()
            },
        },
        methods: {
            getRate: function (day, time, user_id) {
                if (day >= 0 && day < this.days) {
                    return this.rating[day][time][user_id]
                } else {
                    return null
                }
            },
            getUsersFromPicture: function () {
                axios
                    .post(this.urls.scan, {
                        url: this.picture_url,
                    })
                    .then(response => {
                        let json = response.data
                        this.raw_users_from_picture = []
                        this.char_height = json.char_height
                        json.users.forEach((item) => {
                            let user = this.users.find((user) => {
                                return user.id == item.id
                            })
                            if (user) {
                                if (this.time) {
                                    user.new_rate.evening = item.rate
                                } else {
                                    user.new_rate.morning = item.rate
                                }
                            }
                        })
                        json.unknown_names.forEach((item) => {
                            this.raw_users_from_picture.push(item)
                        })
                        this.picture_url = ''
                    })
            },
            writeUserHash: function (index) {
                if (this.selected_user !== null) {
                    let item = this.raw_users_from_picture[index]
                    this.users[this.selected_user].hashes.push(item.hash)
                    if (this.time) {
                        this.users[this.selected_user].new_rate.evening = item.rate
                    } else {
                        this.users[this.selected_user].new_rate.morning = item.rate
                    }
                    this.raw_users_from_picture.splice(index, 1)
                    this.selected_user = null
                }
            },
            getRawJson: function () {
                axios
                    .get(this.urls.tatsu_top)
                    .then(response => (this.raw_json = JSON.stringify(response.data)))
            },
            getUsersFromJson: function () {
                let json = JSON.parse(this.raw_json).rankings
                this.raw_users_from_json = []
                json.forEach((item) => {
                    let user = this.users.find((user) => {
                        return user.discord_id == item.user_id
                    })
                    if (user) {
                        if (this.time) {
                            user.new_rate.evening = item.score
                        } else {
                            user.new_rate.morning = item.score
                        }
                    } else {
                        this.raw_users_from_json.push({
                            rank: item.rank,
                            id: item.user_id,
                            name: null,
                            score: item.score,
                        })
                    }
                });
            },
            getUserData: function (index) {
                if (!this.raw_users_from_json[index].name) {
                    axios
                        .get(`${this.usrl.discord_user}/${this.raw_users_from_json[index].id}`)
                        .then(response => (this.raw_users_from_json[index].name = response.data.username))
                }
            },
            writeUserId: function (index) {
                if (this.selected_user !== null) {
                    let item = this.raw_users_from_json[index]
                    this.users[this.selected_user].discord_id = item.id
                    this.users[this.selected_user].changed_id = true
                    if (this.time) {
                        this.users[this.selected_user].new_rate.evening = item.score
                    } else {
                        this.users[this.selected_user].new_rate.morning = item.score
                    }
                    this.raw_users_from_json.splice(index, 1)
                    this.selected_user = null
                }
            },
            clearInputs: function () {
                this.users.forEach((user) => {
                    user.new_rate.morning = null;
                    user.new_rate.evening = null;
                })
            },
            exchangeInputs: function () {
                let temp
                for (user of this.users) {
                    temp = user.new_rate.morning
                    user.new_rate.morning = user.new_rate.evening
                    user.new_rate.evening = temp
                }
            },
            saveData: function () {
                axios
                    .post(this.urls.load, {
                        users: this.users,
                        date: this.current_date,
                    })
                    .then(response => {
                        console.log(response.data)
                        this.users.forEach((user, index) => {
                            user.hashes = []
                            if (user.new_rate.morning !== null) {
                                this.rating[this.current_day][0][index] = user.new_rate.morning
                                user.new_rate.morning = null
                            }
                            if (user.new_rate.evening !== null) {
                                this.rating[this.current_day][1][index] = user.new_rate.evening
                                user.new_rate.evening = null
                            }
                            user.changed_id = false
                        });
                        this.current_day++
                        this.time = false
                    })
            }
        }
    })
}
