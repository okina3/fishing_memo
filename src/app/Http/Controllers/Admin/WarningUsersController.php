<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteUserRequest;
use App\Http\Requests\Admin\IndexUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarningUsersController extends Controller
{
    /**
     * 警告したユーザー一覧を表示するメソッド。
     * @param IndexUserRequest $request
     * @return View
     */
    public function index(IndexUserRequest $request): View
    {
        // 警告したユーザーを取得する
        $all_warning_users = User::onlyTrashed()->searchKeyword($request->keyword)->availableAllUsers()->get();

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
        User::onlyTrashed()->availableSelectUser($request->userId)->forceDelete();

        return to_route('admin.warning.index')->with(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'success']);
    }
}
