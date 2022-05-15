<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\MemoTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        //タグ一覧を取得
        $tags = Tag::where('user_id', '=', Auth::id())
        ->whereNull('deleted_at')
        ->orderBy('id', 'DESC')
        ->get();
        return view('create', compact('memos', 'tags'));
    }

    public function store(Request $request)
    {
        //postで投げられた値を全て受け取る
        $posts = $request->all();
        //dump dieの略 ⇨ メソッドの引数の取った値を展開する ⇨ データの確認用
        // dd($posts);

        //トランザクションの開始
        DB::transaction(function () use($posts){
            //memosテーブルへインサート
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => Auth::id()]);
            //新規タグが既にtagsテーブルに存在するかのチェック
            $tag_exists = Tag::where('user_id', '=', Auth::id())
            ->where('name', '=', $posts['new_tag'])
            ->exists();
            //新規タグが入力されているかチェック
            if( !empty($posts['new_tag']) && !$tag_exists){
                //新規タグが既に存在していなければtagsテーブルにインサートしてtag_idの取得
                $tag_id = Tag::insertGetId(['user_id' => Auth::id(), 'name' => $posts['new_tag']]);
                //memo_tagsテーブルにインサート
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
            }
            //既存タグを受信した場合⇨memo_tagsにインサート
            foreach ($posts['tags'] as $tag ){
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
            }
        });
        //トランザクション終了

        
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

    public function update(Request $request)
    {
        //postで投げられた値を全て受け取る
        $posts = $request->all();
        //dump dieの略 ⇨ メソッドの引数の取った値を展開する ⇨ データの確認用
        // dd($posts);
        // dd(Auth::id());

        Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content']]);
        
        
        return redirect( route('home') );
    }

    public function destroy(Request $request)
    {
        //postで投げられた値を全て受け取る
        $posts = $request->all();
        //dump dieの略 ⇨ メソッドの引数の取った値を展開する ⇨ データの確認用
        // dd($posts);
        // dd(Auth::id());

        //SQLのdelete文を記述すると物理削除になる
        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);
        
        
        return redirect( route('home') );
    }

}
