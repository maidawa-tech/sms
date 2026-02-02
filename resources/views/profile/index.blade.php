@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid mt-3">

    <h4 class="mb-2" style="color:#446644">User Profile</h4>

    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header text-white" style="background:#fff;">
                    <h6 class="mb-0" style="color:#000;">Profile Photo</h6>
                </div>

                <div class="card-body text-center">
                    @php
                        $avatar = $user->avatar ? asset('uploads/users/'.$user->avatar) : asset('default-user.png');
                    @endphp

                    <img src="{{ $avatar }}" id="avatarPreview"
                        class="rounded-circle mb-3"
                        style="width:140px; height:140px; object-fit:cover; border:4px solid #E6F2E6;">
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" class="form-control mb-2" onchange="previewAvatar(event)">
                        <button class="btn text-white w-100" style="background:#446644;">Update Photo</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header text-white" style="background:#fff;">
                    <h6 class="mb-0" style="color:#000;">Personal Information</h6>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" value="{{ $user->name }}"
                                    class="form-control border-success">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ $user->email }}"
                                    class="form-control border-success">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" value="{{ $user->phone }}"
                                    class="form-control border-success">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" value="{{ $user->address }}"
                                    class="form-control border-success">
                            </div>
                        </div>

                        <button class="btn text-white px-4" style="background:#446644;">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- PASSWORD SECTION -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header text-white" style="background:#fff;">
                    <h6 class="mb-0" style="color:#000;">Change Password</h6>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control border-success">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control border-success">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control border-success">
                        </div>

                        <button class="btn btn-warning px-4">Update Password</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
function previewAvatar(event) {
    document.getElementById('avatarPreview').src = URL.createObjectURL(event.target.files[0]);
}
</script>
@endsection
