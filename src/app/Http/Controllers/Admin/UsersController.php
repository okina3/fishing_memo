<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteUserRequest;
use App\Http\Requests\Admin\SearchKeywordRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UsersController extends Controller
{
    /**
     * 全ユーザー、また、検索したユーザーを表示するメソッド。
     * @param SearchKeywordRequest $request
     * @return View
     */
    public function index(SearchKeywordRequest $request): View
    {
        // 全ユーザー、また、検索したユーザーをページネーションで取得
        $perPage = 15;
        $all_users = User::availableAllUsers()
            ->searchKeyword($request->keyword)
            ->paginate($perPage)
            ->appends(request()->query());

        return view('admin.users.index', compact('all_users'));
    }

    /**
     * ユーザーのサービス利用を停止（ソフトデリート）するメソッド。
     * @param DeleteUserRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(DeleteUserRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 自分の全てのメモの共有設定を解除する
                UserService::deleteUserShareSettingAll($request->userId);
                // 選択したユーザーのサービス利用を停止
                User::findOrFail($request->userId)->delete();
            }, 10);

            return to_route('admin.index')->with(['message' => 'ユーザーのサービス利用を停止しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'ユーザーのサービス利用停止に失敗しました。', 'status' => 'error']);
        }
    }
}
