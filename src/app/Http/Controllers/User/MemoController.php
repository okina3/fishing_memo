<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MemoController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('user.memos.index');
    }
}
