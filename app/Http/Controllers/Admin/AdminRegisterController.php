<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminRegisterController extends Controller
{
    // 登録画面呼び出し
    public function create(): View
    {
        return view('admin.auth.register');
    }

    // 登録実行
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Admin::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($admin));

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')->with([
            'login_msg' => 'ログインしました。',
        ]);
    }
}
