<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 ">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 ">
            {{ __('Update your profile photo.') }}
        </p>
    </header>
    <div class="flex flex-row items-center justify-center ">
        @php
            $photoUrl = $user->profile_img
                ? route('user.photo', ['filename' => $user->profile_img])
                : asset('images/profile/default-photo.jpg'); // Make sure this file exists
        @endphp
        <div class="mt-6 ml-5 mr-16">
            <x-input-label for="current_photo" :value="__('Current Profile Photo')" />
            <div class="mt-1">
                <img src="{{$photoUrl}}" alt="Profile Photo"
                    class="w-32 h-32 object-cover rounded-full" />
            </div>
        </div>

        <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data"
            class="mt-6 space-y-6">
            @csrf
            @method('PATCH')
            <div>
                <x-input-label for="profile_img" :value="__('Profile Photo')" />
                <input type="file" name="profile_img" id="profile_img" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('profile_img')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-photo-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Saved.') }}
                    </p>
                @endif
            </div>
        </form>


    </div>
</section>