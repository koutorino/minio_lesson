<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        // $posts = Post::all();
        $posts = Post::all();
        return view('index', compact('posts'));
    }

    public function create()
    {
        return view('create');
    }

    // createじゃ何故かうごかなかった。
    public function store(Request $request)
    {
        //タイトルとコメントの処理
        $title = $request->input('title');
        $content = $request->input('content');

        //画像の処理
        $file = $request->file('image');
        $path = Storage::disk('s3')->putFile('/', $file, 'public');
        $path = Storage::disk('minio')->url($path);
        $pathArray = explode('/', $path);
        $pathArray[2] = 'localhost:9000';
        $newPath = implode('/', $pathArray);

        Post::insert([
            'user_id' => 1,
            'title' => $title,
            'content' => $content,
            'image' => $newPath,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/');
    }

    public function show()
    {
        $post = Post::where('id', 1)->first();
        return view('show', compact('post'));
    }

    public function update(Int $id, Request $request)
    {
        //タイトルとコメントの処理
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->content = $request->input('content');

        //画像の処理
        $file = $request->file('file');
        $path = Storage::disk('s3')->putFile('/', $file, 'public');
        $path = Storage::disk('minio')->url($path);
        $pathArray = explode('/', $path);
        $pathArray[2] = 'localhost:9000';
        $newPath = implode('/', $pathArray);
        $post->save();
        return response()->json($post);
    }

    public function destroy(Int $id)
    {
        Post::find($id)->delete();
        return response()->json(Post::all());
    }
}
