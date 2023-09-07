<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);

        // jika tidak ada post
        if (!$post) {
            return response([
                'message' => 'Post not found..',
            ], 404);
        }

        return response([
            'post' => $post->comments()->with('user:id,name,email')->get()
        ], 200);
    }

    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        // jika tidak ada post
        if (!$post) {
            return response([
                'message' => 'Post not found..',
            ], 404);
        }

        $attr = $request->validate([
            'comment' => 'required|string',
        ]);

        Comment::create([
            'comment' => $attr['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment created..',
        ], 200);
    }
}
