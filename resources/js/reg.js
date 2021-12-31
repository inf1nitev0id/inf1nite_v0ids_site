window.onload = function () {
    const reg_form = new Vue({
        el: '#reg',
        data: {
            errors: [],
            login: null,
            password: null,
            password_repeat: null,
            invite: null
        },
        methods: {
            checkForm: function (e) {
                this.errors = [];

                if (this.login.length < 3 || this.login.length > 20) {
                    this.errors.push('Имя пользователя должно быть длиной от 3 до 20 символов.');
                }

                if (this.password != this.password_repeat) {
                    this.errors.push('Пароли должны совпадать.');
                }

                if (this.invite.length != 10) {
                    this.errors.push('Код приглашения должен состоять из 10 символов.');
                }

                if (!this.errors.length) {
                    return true;
                }

                e.preventDefault();
            }
        }
    })
}
