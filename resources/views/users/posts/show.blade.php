@extends('layouts.app')

@section('title', 'Show Post')

@section('content')
<style>
    .col-4{
        overflow-y: scroll;
    }
    .card-body{
        position: absolute;
        top: 65px;
    }
</style>

<div class="row border shadow">
    <div class="col p-0 border-end">
        <img src="{{ asset('/storage/images/' . $post->image)}}" alt="{{ $post->image }}" class="w-100">
    </div>
    <div class="col-4 px-0 bg-white">
        <div class="card border-0">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <!-- user avatar -->
                        <a href="{{ route('profile.show', $post->user->id)}}">
                            @if($post->user->avatar)
                                <img src="{{ asset('storage/avatars/' . $post->user->avatar) }}" alt="{{ $post->user->avatar }}" class="rounded-circle avatar-sm">
                            @else
                                <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                            @endif
                        </a>
                    </div>
                    <div class="col ps-0">
                        <!-- user name -->
                        <a href="{{ route('profile.show', $post->user->id)}}" class="text-decoration-none text-dark">{{ $post->user->name }}</a>
                    </div>
                    <div class="col-auto">
                        <!-- if you are the owner of the post, you can edit or delete the post -->
                        @if(Auth::user()->id === $post->user->id)
                            <!-- dropdown for edit and delete -->
                            <div class="dropdown">
                                <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('post.edit', $post->id) }}" class="dropdown-item">
                                        <i class="fa-regular fa-open-to-swuare"></i> Edit
                                    </a>
                                    <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete-post-{{ $post->id}}">
                                        <i class="fa-regular fa-trash-can"></i> Delete
                                    </button>
                                </div>
                                @include('users.posts.contents.modals.delete')
                            </div>
                        @else
                        <!-- if you are not the owner of the post, show Follow/Unfollow button  -->
                            @if($post->user->isFollowed())
                                <form action="{{ route('follow.destroy', $post->user->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent p-0 text-secondary">Following</button>
                                </form>
                            @else
                                <form action="{{ route('follow.store', $post->user->id) }}" method="post" class="d-inline">
                                    @csrf
                                    <button type="submit" class="border-0 bg-transparent p=0 text-primary">Follow</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body w-100">
                <!-- heart button, number of likes, categories -->
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($post->isLiked())
                            <form action="{{ route('like.destroy', $post->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm shadow-none p-0">
                                    <i class="fa-solid fa-heart text-danger" ></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('like.store', $post->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm shadow-none p-0">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="col-auto px-0">
                        <span>{{ $post->likes->count() }}</span>
                    </div>
                    <div class="col text-end">
                        @forelse($post->categoryPost as $category_post)
                            <div class="badge bg-secondary bg-opacity-50">
                                {{ $category_post->category->name }}
                            </div>
                        @empty
                            <div class="badge bg-secondary bg-opacity-50">
                            Uncategorized
                            </div>
                        @endforelse
                    </div>
                </div>
                <!-- owner , description -->
                <a href="{{ route('profile.show', $post->user->id)}}" class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                &nbsp;
                <p class="d-inline fw-light">{{ $post->description }}</p>
                <p class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($post->created_at)) }}</p>
                <!-- comments -->
                <div class="mt-4">
                <form action="{{ route('comment.store', $post->id)}}" method="post">
                    @csrf
                    <div class="input-group">
                        <textarea name="comment_body{{ $post->id}}" rows="1" 
                        placeholder="Write your comment here."  
                        class="form-control form-control-sm" >
                          
                        </textarea>
                        <button type="submit" 
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fa-sharp fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    @error('comment_body' . $post->id)
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </form>
                    <!-- comments whill show here -->
                    @if($post->comments->isNotEmpty())
                
                        <ul class="list-group mt-2">
                            @foreach($post->comments as $comment)
                                <li class="list-group-item border-0 p-0 mb-2">
                                    <a href="route('profile.show', $comment->user->id)}}" class="text-decoration-none text-dark fw-bold">{{ $comment->user->name}}</a>
                                    &nbsp;
                                    <p class="d-inline fw-light">{{ $comment->body }}</p>
                                    <form action="{{ route('comment.destroy', $comment->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        <span class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($comment->created_at))}}</span>
                                        <!-- if the auth user is the owner of the comment, show delete button -->
                                        @if(Auth::user()->id === $comment->user->id)
                                            &middot;
                                            <button type="submit" class="border-0 bg-transparent text-danger p-0 xsmall">Delete</button>
                                        @endif
                                    </form>
                                </li>
                            @endforeach
                           
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection