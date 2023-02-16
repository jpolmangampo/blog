<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    const LOCAL_STORAGE_FOLDER = 'public/images/';
    private $post;
    private $category;

    public function __construct(Post $post, Category $category){
        $this->post     = $post;
        $this->category = $category;
    }

    public function create(){
        $all_categories = $this->category->all();
        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    public function saveImage($request){
        $image_name = time() . "." . $request->image->extension();
        // image.jpg
        // $image_name = 1568765648.jpg
        $request->image->storeAs(self::LOCAL_STORAGE_FOLDER, $image_name);
        return $image_name;

    }

    public function store(Request $request){
        $request->validate([
            'category'      => 'required|array|between:1,3',
            'description'   => 'required|min:1|max:1000',
            'image'         => 'required|mimes:jpg,png,jpeg,gif|max:1048'
        ]);

        #Save the post
        $this->post->user_id     = Auth::user()->id;
        $this->post->image       = $this->saveImage($request);
        $this->post->description = $request->description;
        $this->post->save();

        #Save the categories to the category_post pivot table
        foreach($request->category as $category_id){
            $category_post[] = ['category_id' => $category_id];
        }
        $this->post->categoryPost()->createMany($category_post);
        
        return redirect()->route('index');
    }

    public function show($id){
        $post = $this->post->findOrFail($id);

        return view('users.posts.show')->with('post', $post);
    }

    public function edit($id){
        $post = $this->post->findOrFail($id);

        if(Auth::user()->id != $post->user->id){
            return redirect()->route('index');
        }

        $all_categories = $this->category->all();

        $selected_categories = [];
        foreach($post->categoryPost as $category_post){
            $selected_categories[] = $category_post->category_id;
        }
        return view('users.posts.edit')
            ->with('post', $post)
            ->with('all_categories', $all_categories)
            ->with('selected_categories', $selected_categories);  

    }

    public function update(Request $request, $id){
        // validate data from the form
        $request->validate([
            'category'      => 'required|array|between:1,3',
            'description'   => 'required|min:1|max:1000',
            'image'         => 'mimes:jpg,png,jpeg,gif|max:1048'
        ]);

        // Update Post
        $post   = $this->post->findOrFail($id);
        $post->description  = $request->description;

        // if there is a new image to upload
        if($request->image){
            // delete the previous image from local storage
            $this->deleteImage($post->image);
            // move the new image to the local storage
            $post->image = $this->saveImage($request);
        }

        $post->save();
        // delete all records from categoryPost related to this post
        $post->categoryPost()->delete();
        // save the new categories to category_post pivot table
        foreach($request->category as $category_id){
            $category_post[] = ['category_id' => $category_id];
        }
        $post->categoryPost()->createMany($category_post);

        return redirect()->route('post.show', $id);

    }

    public function deleteImage($image_name){
        $image_path = self::LOCAL_STORAGE_FOLDER . $image_name;
        //6485486.png
        // $image_path = "/public/images/6485486.png"

        if(Storage::disk('local')->exists($image_path)){
            Storage::disk('local')->delete($image_path);
        }
    }

    public function destroy($id){
        $post = $this->post->findOrFail($id);
        $this->deleteImage($post->image);
        $post->forceDelete();
        return redirect()->route('index');
    }

    

   

}
