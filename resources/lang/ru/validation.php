<?php
return [
  'required' => 'Поле ":attribute" является обязательным.',
  'max' => [
      'string' => 'Поле ":attribute" не должно быть длиннее :max символов.',
  ],
  'min' => [
      'string' => 'Поле ":attribute" не должно быть короче :min символов.',
  ],
  'alpha_dash' => 'Поле ":attribute" может содержать только буквы, цифры, "-" и "_".',
  'email' => 'Поле ":attribute" должно содержать e-mail адрес.',
  'same' => 'Пароли должны совпадать.',
  'size' => [
    'string' => 'Поле ":attribute" должно состоять из :size символов.',
  ],
  'url' => 'Формат :attribute неверный.',
  'date' => 'Поле ":attribute" содержит неправильную дату.',

  'attributes' => [
    'login' => 'Логин',
    'email' => 'Почта',
    'password' => 'Пароль',
    'password-repeat' => 'Повтор пароля',
    'title' => 'Заголовок',
    'text' => 'Текст',
    'invite' => 'Код приглашения',
    'url' => 'URL',
    'date' => 'Дата',
    'time' => 'Время'
  ],
];
