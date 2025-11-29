<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        // 管理者は1人のみ許可する為、既に存在する場合はログイン画面へリダイレクト
        if (Admin::count() > 0) {
            return to_route('admin.login')->with('status', '管理者は既に登録されています。');
        }

        return view('admin.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 管理者は1人のみ許可する為、既に存在する場合はログイン画面へリダイレクト
        if (Admin::count() > 0) {
            return to_route('admin.login')->with('status', '管理者は既に登録されています。');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        try {
            $admin = DB::transaction(function () use ($request) {
                // 再確認。（管理者は1人のみ許可する）
                if (Admin::count() > 0) {
                    throw new \RuntimeException('既に管理者が存在します。');
                }

                return Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            }, 5);

            event(new Registered($admin));
            Auth::guard('admin')->login($admin);

            return redirect(route('admin.dashboard', absolute: false));
        } catch (Throwable $e) {
            Log::error($e);
            return to_route('admin.login')->with('status', '管理者登録に失敗しました。');
        }
    }
}
