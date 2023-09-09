<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')
                ->with('user:id,name,image')
                ->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')
                        ->get();
                })
                ->get(),
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
            'body'  => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body'      => $attr['body'],
            'user_id'   => auth()->user()->id,
            'image'     => $image,
        ]);


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

        if ($request->has('image') && !empty($request->image)) {

            $path = parse_url($post->image, PHP_URL_PATH);

            // hapus foto lama
            if ($post->image <> null) {
                unlink(public_path() . $path);
            }

            $image = $this->saveImage($request->image, 'posts');

            $post->update([
                'body'      => $attr['body'],
                'image'     => $image
            ]);
        } else {
            $post->update([
                'body'      => $attr['body'],
            ]);
        }

        return response([
            'message'   => 'Post updated',
            'post'      => $post,
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

        $path = parse_url($post->image, PHP_URL_PATH);



        // hapus foto lama
        if ($post->image <> null) {
            unlink(public_path() . $path);
        }

        // delete
        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted',
        ], 200);
    }
}
