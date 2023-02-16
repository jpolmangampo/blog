@extends('layouts.app')

@section('title', 'Admin: Posts')
    
@section('content')
@auth
<div class="mb-2 ml-auto d-flex justify-content-end">
    <i class="fa-solid fa-magnifying-glass text-dark "></i>&nbsp; 
    <form action="{{ route('admin.posts') }}" style="width:300px">
       <input type="search" style="width:300px" name="search" id="" class="form-control form-control-sm" placeholder="Search for name(s)" value="{{ old('search', $search) }}">
    </form>  
</div>
@endauth
<table class="table table-hover align-middle bg-white border text-secondary">
    <thead class="table-primary text-secondary">
        <tr>
            <th></th>
            <th></th>
            <th>CATEGORY</th>
            <th>OWNER</th>
            <th>CREATED AT</th>
            <th>STATUS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($all_posts as $post)
            <tr>
                <td class="text-end">
                    {{ $post->id }}
                </td>
                <td>
                    <a href="{{ route('post.show', $post->id) }}">
                        <img src="{{ asset('storage/images/' . $post->image) }}" alt="{{ $post->image }}" class="d-block mx-auto avatar-md">
                    </a>
                </td>
                <td>
                    @forelse($post->categoryPost as $category_post)
                            <div class="badge bg-secondary bg-opacity-50">
                                {{ $category_post->category->name }}
                            </div>
                    @empty
                            <div class="badge bg-secondary bg-opacity-50">
                            Uncategorized
                            </div>
                    @endforelse
                </td>
                <td>
                    <a href="{{ route('profile.show', $post->user->id) }}" class="text-decoration-none text-dark">
                        {{ $post->user->name }}
                    </a>
                </td>
                <td>
                    {{ $post->created_at }}
                </td>
                <td>
                    @if ($post->trashed())
                    <i class="fa-solid fa-circle-minus text-secondary"></i>&nbsp; Hidden  
                    @else
                    <i class="fa-solid fa-circle text-primary"></i>&nbsp; Visible
                    @endif
                    
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        @if ($post->trashed())
                        <div class="dropdown-menu">
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#unhide-post-{{ $post->id }}">
                                <i class="fa-solid fa-eye"></i> Unhide Post {{ $post->id }}
                            </button>
                        </div> 
                        @else
                        <div class="dropdown-menu">
                            <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#hide-post-{{ $post->id }}">
                                <i class="fa-solid fa-eye-slash"></i> Hide Post {{ $post->id }}
                            </button>
                        </div>    
                        @endif 
                        
                    </div>

                </td>
            </tr>
            @include('admin.posts.modal.status')
        @empty
            <tr>
                <td colspan="7" class="leada text-muted text-center">No posts found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{ $all_posts->appends(request()->query())->links() }}
@endsection