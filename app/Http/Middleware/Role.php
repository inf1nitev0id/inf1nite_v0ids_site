<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next, $role)
  {
    if (Auth::check()) {
      $ok = false;
      switch (Auth::user()->role) {
        case 'admin':
          $ok = true;
          break;
        case 'moderator':
          $ok = $role == 'moderator';
          break;
        case 'user':
          $ok = false;
          break;
      }
      if ($ok) return $next($request);
    }

    return redirect()->back()->withErrors(["У вас нет прав для этого действия."]);
  }
}
