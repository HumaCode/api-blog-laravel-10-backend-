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

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        // jika tidak ada post
        if (!$post) {
            return response([
                'message' => 'Post not found..',
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied..',
            ], 403);
        }

        $attr = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body'      => $attr['body'],
        ]);

        // lewati upload gambar

        return response([
            'message' => 'Post updated',
            'post' => $post,
        ], 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        // jika tidak ada post
        if (!$post) {
            return response([
                'message' => 'Post not found..',
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied..',
            ], 403);
        }

        // delete
        $post->comments->delete();
        $post->likes->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted',
        ], 200);
    }
}
