<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'userall', 'updateprofile']]);
    }
    public function index()
    {
        return PostResource::collection(Post::inRandomOrder()->get());
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->text = $request->get('text');
        $post->user_id = auth()->user()->id;

        if ($request->hasFile('file') && $request->file('file') !== null && $request->file('file') !== "") {
            Storage::disk('public')->delete(str_replace('storage/', '', $post->image));
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $post->image = 'storage/' . $filePath;
        } else {

        }
        $post->save();
        return response()->json([
            "data" => $post,
            "message" => "Update Post"
        ]);
    }
    public function destroy($id)
    {
        $post = Post::find($id);
        Storage::disk('public')->delete(str_replace('storage/', '', $post->image));
        $comment = Comment::where('post_id', $id)->where('user_id', auth()->user()->id);
        $comment->delete();
        $post->delete();
        return response()->json([
            "message" => "Delete Post"
        ]);
    }
    public function store(Request $request)
    {
        $post = new Post;
        $post->text = $request->input('text');

        if ($request->hasFile('file') && $request->file('file') !== null) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $post->image = 'storage/' . $filePath;
        }
        $post->user_id = auth()->user()->id;
        $post->save();
        return response()->json([
            "data" => $post,
            "message" => "Create Post"
        ]);
    }


    public function mypost()
    {
        $userId = auth()->user()->id;
        $posts = Post::where('user_id', $userId)->get(); // or ->first() if you expect only one cart

        return PostResource::collection($posts);
    }

    public function show($id)
    {
        return new PostResource(Post::findOrFail($id));
    }

    public function usercomment(Request $request)
    {
        $comment = new Comment;
        $comment->post_id = $request->input('post_id');
        $comment->text = $request->input('text');
        $comment->user_id = auth()->user()->id;
        $comment->save();

        $comments = Comment::where('post_id', $request->input('post_id'))->get();
        $commentData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'image' => $comment->user->image ? url($comment->user->image) : null,
                    'email' => $comment->user->email,
                ],
                'text' => $comment->text,
                'created_at' => $comment->created_at->thaidate('d F Y เวลา H:i') . ' น.',
            ];
        });

        // Retrieve user information for the post owner
        $postUser = User::find($request->user()->id);

        $userData = [
            'id' => $postUser->id,
            'name' => $postUser->name,
            'image' => $postUser->image ? url($postUser->image) : null,
            'email' => $postUser->email,
        ];

        return response()->json([
            'message' => 'Comment success',
            'comments' => $commentData->toArray(),
            'user' => $userData,
        ]);
    }

    public function userall()
    {
        $users = User::all(['name']);
        return response()->json($users);
    }

    public function updateprofile(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->get('name');
        if ($request->hasFile('file') && $request->file('file') !== null && $request->file('file') !== "") {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('profile', $fileName, 'public');
            $user->image = 'storage/' . $filePath;
        }
        $user->save();
        return response()->json([
            "message" => "update profile success"
        ]);
    }

    public function deletecomment($id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        return response()->json([
            "message" => "delete comment success"
        ]);
    }
}
