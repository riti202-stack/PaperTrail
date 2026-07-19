@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <h2 class="font-display text-2xl text-ink">Profile</h2>
        <p class="text-envelope text-sm mt-0.5">Manage your account information and password</p>
    </div>

    <div class="space-y-6">
        <div class="p-6 bg-white border border-ink/10 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-ink/10 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-ink/10 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection