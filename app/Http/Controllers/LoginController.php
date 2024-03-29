<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Invite;

/**
 * Контроллер аутентификации
 */
class LoginController extends Controller {
    /**
     * @param $from
     *
     * @return string
     */
    public static function getFrom($from): string {
        switch ($from) {
            case 'mahouka':
                return route('mahouka.home');
            default:
                return route('home');
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request): \Illuminate\Http\RedirectResponse {
        if (
            !Auth::attempt(
                ['email' => $request->login, 'password' => $request->password],
                $request->rememberme
            ) &&
            !Auth::attempt(
                ['name' => $request->login, 'password' => $request->password],
                $request->rememberme
            )
        ) {
            return redirect()
                ->back()
                ->with(
                    'authError',
                    "Неправильные логин или пароль."
                );
        }
        return redirect()->intended($this->getFrom($request->from));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request): \Illuminate\Http\RedirectResponse {
        $request->validate([
                               'login'    => 'required|alpha_dash|min:3|max:20',
                               'email'    => 'required|email',
                               'password' => 'required|same:password-repeat',
                               'invite'   => 'required|size:10',
                           ]);

        $query        = Invite::select(
            'id',
            'usages'
        )
            ->where(
                'code',
                '=',
                $request->invite
            )
            ->first();
        $invite_id    = null;
        $invite_error = false;
        if ($query != null) {
            $invite_id    = $query->id;
            $invite_error = $query->usages != null &&
                User::select('id')
                    ->where(
                        'invite_id',
                        '=',
                        $query->id
                    )
                    ->count() >= $query->usages;
        } else {
            $invite_error = true;
        }
        if ($invite_error) {
            return redirect()
                ->back()
                ->withErrors(["Некорректный код приглашения."]);
        }

        $query = User::select('id')
            ->where(
                'name',
                '=',
                $request->login
            )
            ->first();
        if ($query != null) {
            return redirect()
                ->back()
                ->withErrors(["Пользователь с таким именем уже существует."]);
        }
        $query = User::select('id')
            ->where(
                'email',
                '=',
                $request->email
            )
            ->first();
        if ($query != null) {
            return redirect()
                ->back()
                ->withErrors(["Пользователь с такой почтой уже существует."]);
        }

        $user            = new User;
        $user->name      = $request->login;
        $user->email     = $request->email;
        $user->password  = Hash::make($request->password);
        $user->invite_id = $invite_id;
        $user->save();
        Auth::loginUsingId($user->id);

        return redirect()->intended($this->getFrom($request->from));
    }
}
