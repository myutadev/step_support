<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // return $request->expectsJson() ? null : route('login');
        if (!$request->expectsJson()) {
            //パスがadmin/から始まる場合、管理者ログイン画面へリダイレクト
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        }
    }
}
