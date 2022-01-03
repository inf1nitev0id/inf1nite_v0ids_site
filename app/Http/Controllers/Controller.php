<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Базовый контроллер
 */
class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return bool
     */
    public static function isModerator(): bool {
        return Auth::check() && Auth::user()->isModerator();
    }

    private static array $submenu = [
        [
            'label'  => 'Махорка',
            'route'  => 'mahouka.home',
            'hidden' => 'mahouka',
        ],
    ];

    public static function getSubmenu(): array {
        return array_filter(self::$submenu, static function($item) {
            return !$item['hidden'] || in_array($item['hidden'], session('hiddenMenu') ?? []);
        });
    }
}
