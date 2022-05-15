<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //ここでメモ一覧を取得
        $memos = Memo::select('memos.*')
            ->where('user_id', '=', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();
        // dd($memos);
        return view('create', compact('memos'));
    }

    public function store(Request $request)
    {
        //postで投げられた値を全て受け取る
        $posts = $request->all();
        //dump dieの略 ⇨ メソッドの引数の取った値を展開する ⇨ データの確認用
        // dd($posts);
        // dd(Auth::id());

        Memo::insert(['content' => $posts['content'], 'user_id' => Auth::id()]);
        
        return redirect( route('home') );
    }

    public function edit($id)
    {
        //ここでメモ一覧を取得
        $memos = Memo::select('memos.*')
            ->where('user_id', '=', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();
        // dd($memos);

        $edit_memo = Memo::find($id);
        return view('edit', compact('memos', 'edit_memo'));
    }
}
