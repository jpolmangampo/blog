<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function index(Request $request){
        $all_posts = $this->post
        ->where('created_at', '=',  $request->search)
        ->orWhere('description','like', '%' . $request->search . '%')
        ->withTrashed()->latest()->paginate(10);

        return view('admin.posts.index')->with('all_posts', $all_posts)->with('search', $request->search);
    }

    public function hide($id){
        $this->post->destroy($id);
        return redirect()->back();
    }

    public function unhide($id){
        $this->post->onlyTrashed()->findOrFail($id)->restore();
        return redirect()->back();
    }


}
