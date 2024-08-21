@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6"> 
        <!-- Profile Information -->

        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            @include('profile.partials.update-profile-information-form')
        </section>

        <!-- Update Password -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            @include('profile.partials.update-password-form')
        </section>

        <!-- Delete User Form -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            @include('profile.partials.delete-user-form')
        </section>
        
    </div>
@endsection
