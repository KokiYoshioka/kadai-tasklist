<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

use app\User;    // 追加

class TasksController extends Controller
{
    public function __construct(){
    $this->middleware('auth');
  }
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        
        /// メッセージ一覧を取得
        $tasks = Task::where('user_id', \Auth::user()->id)->get();

        // メッセージ一覧ビューでそれを表示
       
            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
        
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        // タスクを作成
        $task = new Task;
        $task->user_id = \Auth::id(); // 一行追加
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスク詳細ビューでそれを表示
        if (\Auth::id() === $task->user_id) {
            return view('tasks.show', [
           'task' => $task,
            ]);
        } else {
            // トップページへリダイレクトさせる
            return redirect('/');
        }
    }

    // getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスク編集ビューでそれを表示
        if (\Auth::id() === $task->user_id) {
            return view('tasks.edit', [
            'task' => $task,
            ]);
        } else {
            // トップページへリダイレクトさせる
            return redirect('/');
        }
    }

    // putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、タスクを更新
        if (\Auth::id() === $task->user_id) {
            $task->status = $request->status;    // 追加
            $task->content = $request->content;
            $task->save();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}