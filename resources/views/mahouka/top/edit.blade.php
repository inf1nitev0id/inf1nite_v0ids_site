@extends('layouts.mahouka')

@section('title') Данные рейтинга @endsection

@section('head')
    <link rel="stylesheet" href="/css/edit.css"/>
    <script>
        var min_date = new Date('{{ $min_date->format("Y-m-d") }}')
        var users = @json($users)

        var rating = @json($rating)

        var urls = {
            scan: '{{ route("mahouka.top.scan") }}',
            tatsu_top: '{{ route("mahouka.top.tatsu_top") }}',
            discord_user: '{{ route("mahouka.top.discord_user") }}',
            load: '{{ route("mahouka.top.load") }}',
        }
    </script>
    <script src="/js/vue.js"></script>
    <script src="/js/edit_rate.js"></script>
@endsection

@section('content')
    <h5>Изменение данных рейтинга</h5>
    <div id="edit_form" class="row">
        <div class="col-xs-12 col-md-6">
            <div class="btn-group">
                <a class="btn btn-outline-secondary" :class="{active: !time}" @click="time = false">Утро</a>
                <a class="btn btn-outline-secondary" :class="{active: time}" @click="time = true">Вечер</a>
            </div>
            <form>
                @csrf
                <div class="form-group">
                    <label for="url-input">Ссылка на картинку</label>
                    <div class="input-group btn-group">
                        <input type="url" class="form-control" id="url-input" name="url" placeholder="URL"
                               v-model="picture_url"/>
                        <a class="btn btn-outline-secondary mini-btn" @click="getUsersFromPicture">Загрузить</a>
                        <div class="btn btn-outline-secondary mini-btn icon-btn" @click="picture_url = ''"><i
                                class="fas fa-broom"></i></div>
                    </div>
                </div>
                <img v-if="picture_url" :src="picture_url">
            </form>

            <table>
                <tr v-for="(user, index) in raw_users_from_picture">
                    <td>
                        <svg :vievBox="'0 0 ' + user.picture.length + ' ' + char_height"
                             :width="user.picture.length / 2" :height="char_height / 2"
                             xmlns="http://www.w3.org/2000/svg">
                            <template v-for="(column, x) in user.picture">
                                <rect v-for="(pixel, y) in column" v-if="pixel" fill="#000" :x="x / 2" :y="y / 2"
                                      width="0.5" height="0.5"/>
                            </template>
                        </svg>
                    </td>
                    <td>
                        @{{ user.rate }}
                    </td>
                    <td>
                        <a class="btn btn-outline-secondary" @click="writeUserHash(index)"><i
                                class="fas fa-chevron-right"></i></a>
                    </td>
                </tr>
            </table>

            <form>
                <div class="form-group">
                    <label for="json-textarea">JSON</label>
                    <textarea id="json-textarea" class="form-control" rows="10" v-model="raw_json"></textarea>
                </div>
                <div class="form-group btn-group">
                    <a class="btn btn-outline-secondary" @click="getRawJson()">Получить</a>
                    <a class="btn btn-outline-secondary" @click="getUsersFromJson()">Загрузить</a>
                </div>
            </form>

            <table>
                <tr v-for="(user, index) in raw_users_from_json">
                    <td>
                        @{{ user.rank }}
                    </td>
                    <td @click="getUserData(index)">
                        @{{ user.name || user.id }}
                    </td>
                    <td>
                        @{{ user.score }}
                    </td>
                    <td>
                        <a class="btn btn-outline-secondary" @click="writeUserId(index)"><i
                                class="fas fa-chevron-right"></i></a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-xs-12 col-md-6">
            <form>
                <table>
                    <tr class="header">
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-outline-secondary" @click="saveData()">Сохранить</a>
                                <a class="btn btn-outline-secondary mini-btn icon-btn" @click="clearInputs()"><i
                                        class="fas fa-broom"></i></a>
                                <a class="btn btn-outline-secondary mini-btn icon-btn" @click="exchangeInputs()"><i
                                        class="fas fa-exchange-alt"></i></a>
                            </div>
                        </td>
                        <td>
                            <a class="btn btn-outline-secondary" @click="current_day--"><i
                                    class="fas fa-arrow-left"></i></a>
                        </td>
                        <td colspan=2>
                            <a class="btn btn-outline-secondary" title="Выбрать последнюю дату"
                               @click="current_day = days - 1">
                                @{{ formated_date }}
                            </a>
                        </td>
                        <td>
                            <a class="btn btn-outline-secondary" @click="current_day++"><i
                                    class="fas fa-arrow-right"></i></a>
                        </td>
                    </tr>
                    <tr v-for="(user, index) in users">
                        <td :title="user.discord_id" class="btn-group">
                            <a class="btn btn-outline-secondary user-btn" :class="{active: selected_user == index}"
                               @click="selected_user = selected_user == index ? null : index">@{{ user.name }}</a>
                            <a class="btn btn-outline-secondary mini-btn icon-btn" v-if="user.hashes.length > 0"><i
                                    class="fas fa-hashtag"></i></a>
                        </td>
                        <td>
                            @{{ current_rating[index].previous }}
                        </td>
                        <td>
                            <input type="number" :placeholder="current_rating[index].morning"
                                   v-model="user.new_rate.morning"/>
                        </td>
                        <td>
                            <input type="number" :placeholder="current_rating[index].evening"
                                   v-model="user.new_rate.evening"/>
                        </td>
                        <td>
                            @{{ current_rating[index].next }}
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
@endsection
