<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);

        // jika tidak ada post
        if (!$post) {
            return response([
                'message' => 'Post not found..',
            ], 404);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id,
            ], 200);

            return response([
                'message' => 'Liked..',
            ], 200);
        } else {
            $like->delete();

            return response([
                'message' => 'Dislike..',
            ], 200);
        }
    }
}
