{{-- Unhide Post --}}
<div class="modal fade" id="unhide-post-{{  $post->id }}">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header border-primary">
                <h5 class="modal-title text-primary">
                    <i class="fa-solid fa-eye">
                        Unhide Post
                    </i>
                </h5>    
            </div>
            <div class="modal-body">
                <p>Are you sure you want to unhide this post?</p>
                <div class="mt-3">
                    <img src="{{ asset('/storage/images/' . $post->image) }}" alt="{{ $post->image }}" class="admin-posts-img">
                    <p class="mt-1 text-muted">{{ $post->description }}</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <form action="{{ route('admin.posts.unhide', $post->id) }}" method="post">
                    @csrf
                    @method('PATCH')

                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Unhide Post</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Hide Post --}}
<div class="modal fade" id="hide-post-{{  $post->id }}">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fa-solid fa-eye-slash">
                        Hide Post
                    </i>
                </h5>    
            </div>
            <div class="modal-body">
                <p>Are you sure you want to hide this post?</p>
                <div class="mt-3">
                    <img src="{{ asset('/storage/images/' . $post->image) }}" alt="{{ $post->image }}" class="admin-posts-img">
                    <p class="mt-1 text-muted">{{ $post->description }}</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <form action="{{ route('admin.posts.hide', $post->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hide Post</button>
                </form>
            </div>
        </div>
    </div>
</div>