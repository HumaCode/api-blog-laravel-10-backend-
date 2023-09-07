<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
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
        ]);
    }
}
