@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <h1 class="page-title">Dashboard Overview</h1>

    <div class="dashboard-cards">
        <div class="card">
            <div class="card-title">Total Students</div>
            <div class="card-value">8,742</div>
            <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
        </div>

        <div class="card">
            <div class="card-title">School Members</div>
            <div class="card-value">542</div>
            <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        </div>

        <div class="card">
            <div class="card-title">Subjects Offered</div>
            <div class="card-value">35</div>
            <div class="card-icon"><i class="fas fa-book"></i></div>
        </div>

        <div class="card">
            <div class="card-title">Pending Applications</div>
            <div class="card-value">127</div>
            <div class="card-icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
@endsection
