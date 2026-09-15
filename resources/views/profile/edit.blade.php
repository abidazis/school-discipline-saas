<x-app-layout>
    <x-slot name="title">Profile</x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
