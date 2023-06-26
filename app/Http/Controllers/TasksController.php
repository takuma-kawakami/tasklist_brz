<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\User;

class TasksController extends Controller
{
    public function index()
    {
        $data = [];
        
        if(\Auth::check()){
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }

        // メッセージ一覧ビューでそれを表示
        return view('tasks.index', $data);
        
    }
    
    public function create()
    {
        $tasks = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'tasks' => $tasks,
        ]);
    }
    
    public function store(Request $request)
    {
        //バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);
        
        //タスクを作成
        $request->user()->tasks()->create([
            'status' => $request->status,
            'content' => $request->content,
        ]);

        //トップページにリダイレクトさせる
        return redirect('/');
    }
    
    public function show($id)
    {
        $tasks = Task::findOrFail($id);
        
        //認証済みユーザがその投稿者の所有者であれば
        if(\Auth::id() === $tasks->user_id){
        //idの値でタスクを検索して取得
        
        //タスク詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $tasks,
            ]);
        }
        else{
            return redirect('/');
        }

    }
    
    public function edit($id)
    {
        //idの値でタスクを検索して取得
        $tasks = Task::findOrFail($id);
        
        //認証済みユーザがその投稿者の所有者であれば
        if(\Auth::id() === $tasks->user_id){
            if(\Auth::check()){
                return view('tasks.edit', ['tasks' => $tasks ]);
            }
        }
        else{
            return redirect('/');
        }
    }
    
    public function update(Request $request, $id)
    {
        //idの値でタスクを検索して取得
        $tasks = Task::findOrFail($id);
        
        //認証済みユーザがその投稿者の所有者であれば
        if(\Auth::id() === $tasks->user_id){
            $request->validate([
                'content' => 'required|max:255',
                'status' => 'required|max:10',    
            ]);
            
            //タスクを更新
            $tasks->status = $request->status;
            $tasks->content = $request->content;
            $tasks->save();
            //トップページへリダイレクトさせる
            return redirect('/');
        }
        else{
            return redirect('/');
        }
    }
    
    public function destroy($id)
    {
        //idの値でタスクを検索して取得
        $tasks = Task::findOrFail($id);
        
        //認証済みユーザがその投稿者の所有者であれば
        if(\Auth::id() === $tasks->user_id){
            //タスクを削除
            $tasks->delete();
            //トップページへリダイレクト
            return redirect('/');
        }
        else{
            //トップページへリダイレクト
            return redirect('/');
        }
    }
}
