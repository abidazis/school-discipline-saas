<x-app-layout>
    <x-slot name="title">Profil Saya</x-slot>

    <div class="profile-wrapper">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>
</x-app-layout>

<style>
    .profile-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
</style>
