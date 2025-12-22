<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteUserRequest;
use App\Http\Requests\Admin\SearchKeywordRequest;
use App\Models\User;
use App\Services\Admin\WarningUsersService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class WarningUsersController extends Controller
{
    /**
     * 警告したユーザー一覧を表示するメソッド。
     * @param SearchKeywordRequest $request
     * @return View
     */
    public function index(SearchKeywordRequest $request): View
    {
        // 警告したユーザーをページネーションで取得する
        $perPage = 15;
        $all_warning_users = User::onlyTrashed()
            ->searchKeyword($request->keyword)
            ->availableAllUsers()
            ->paginate($perPage)
            ->appends(request()->query());

        return view('admin.warningUsers.index', compact('all_warning_users'));
    }

    /**
     * 警告したユーザーを元に戻すメソッド。
     * @param DeleteUserRequest $request
     * @return RedirectResponse
     */
    public function undo(DeleteUserRequest $request): RedirectResponse
    {
        User::onlyTrashed()->availableSelectUser($request->userId)->restore();

        return to_route('admin.warning.index')->with(['message' => 'ユーザーのサービス利用を再開しました', 'status' => 'success']);
    }

    /**
     * 警告したユーザーを完全削除するメソッド。
     * @param DeleteUserRequest $request
     * @return RedirectResponse
     */
    public function destroy(DeleteUserRequest $request): RedirectResponse
    {
        try {
            WarningUsersService::permanentlyDeleteUser((int) $request->userId);

            return to_route('admin.warning.index')->with(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'ユーザーの完全削除に失敗しました。', 'status' => 'error']);
        }
    }
}
