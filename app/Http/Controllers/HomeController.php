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
        return view('create');
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
}
