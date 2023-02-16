@extends('layouts.app')

@section('title', 'Admin: Users')

@section('content')
    @auth
        <div class="mb-2 ml-auto d-flex justify-content-end">
            <i class="fa-solid fa-magnifying-glass text-dark "></i>&nbsp; 
            <form action="{{ route('admin.users') }}" style="width:300px">
               <input type="search" style="width:300px" name="search" id="" class="form-control form-control-sm" placeholder="Search for name(s)" value="{{ old('search', $search) }}">
            </form>  
        </div>
    @endauth
    <table class="table table-hover align-middle bg-white border text-secondary">
        <thead class="small table-success text-secondary">
            <tr>
                <th></th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>CREATED AT</th>
                <th>STATUS</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($all_users as $user)
                <tr>
                    <td>
                        @if ($user->avatar)
                            <img src="{{ asset('/storage/avatars/' . $user->avatar) }}" alt="{{ $user->avatar }}" class="rounded-circle d-block mx-auto avatar-md">
                        @else
                            <i class="fa-solid fa-circle-user text-secondary d-block text-center icon-md"></i>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('profile.show', $user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $user->name }}</a>
                    </td>
                    <td>
                        {{ $user->email }}
                    </td>
                    <td>
                        {{ $user->created_at }}
                    </td>
                    <td>
                        @if ($user->trashed())
                            <i class="fa-solid fa-circle text-secondary"></i>&nbsp; Inactive
                        @else
                            <i class="fa-solid fa-circle text-success"></i>&nbsp; Active
                        @endif
                        
                    </td>
                    <td>
                        @if (Auth::user()->id !== $user->id)
                        <div class="dropdown">
                            <button class="btn btn-sm" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                            @if ($user->trashed())
                            <div class="dropdown-menu">
                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#activate-user-{{ $user->id }}">
                                    <i class="fa-solid fa-user-check"></i> Activate {{ $user->name }}
                                </button>
                            </div>
                            @else
                            <div class="dropdown-menu">
                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deactivate-user-{{ $user->id }}">
                                    <i class="fa-solid fa-user-slash"></i> Deactivate {{ $user->name }}
                                </button>
                            </div>    
                            @endif
                            
                        </div>
                        @include('admin.users.modal.status')
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $all_users->appends(request()->query())->links() }}
@endsection