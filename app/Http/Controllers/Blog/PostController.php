<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'like')->get(),
        ], 200);
    }

    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        $attr = $request->validate([
            'body' => 'required|string',
        ]);

        $post = Post::create([
            'body'      => $attr['body'],
            'user_id'   => auth()->user()->id,
        ]);

        // lewati upload gambar

        return response([
            'message' => 'Post created',
            'post' => $post,
        ], 200);
    }
}
