<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    @php
        $address = $user->address;
    @endphp
    <form id="profile-form" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4 ml-5">
        @csrf
        @method('patch')
        <x-input-label for="name" :value="__('Name')" />
        <div id="name-section" class="flex flex-row gap-3">
            <div>
                <x-text-input id="name" name="fname" type="text" class="mt-1 block w-full" :value="old('fname', $user->fname)" required autofocus autocomplete="fname" />
                <x-input-error class="mt-2" :messages="$errors->get('fname')" />
            </div>
            <div>
                <x-text-input id="name" name="lname" type="text" class="mt-1 block w-full" :value="old('lname', $user->lname)" required autofocus autocomplete="lname" />
                <x-input-error class="mt-2" :messages="$errors->get('lname')" />
            </div>
        </div>
        <div id="email-section">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div id="phone-section">
            <x-input-label for="name" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone_number" type="text" class="mt-1 block w-full"
                :value="old('phone_number', $address->phone_number)" required autofocus autocomplete="phone_number" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>
        <div id="address-section" class="">
            <x-input-label for="Address" :value="__('Address')" />
            <x-text-input id="address_detail" name="detail" type="text" class="mt-1 block w-full" :value="old('detail', $address->detail)" required autofocus autocomplete="detail" />
            <x-input-error class="mt-2" :messages="$errors->get('detail')" />
            <div class="flex flex-row justify-center items-center gap-3 mt-2">
                <x-text-input id="address_district" name="distric" type="text" class="mt-1 block w-full max-w-[9.5rem]"
                    :value="old('distric', $address->distric)" required autofocus autocomplete="distric" />
                <x-input-error class="mt-2" :messages="$errors->get('distric')" />
                <x-text-input id="address_province" name="province" type="text" class="mt-1 block w-full max-w-[9.5rem]"
                    :value="old('province', $address->province)" required autofocus autocomplete="province" />
                <x-input-error class="mt-2" :messages="$errors->get('province')" />
                <x-text-input id="address_country" name="country" type="text" class="mt-1 block w-full max-w-[9.5rem]"
                    :value="old('country', $address->country)" required autofocus autocomplete="country" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
                <x-text-input id="address_postal_code" name="postal_code" type="text"
                    class="mt-1 block w-full max-w-[9.5rem]" :value="old('postal_code', $address->postal_code)" required
                    autofocus autocomplete="postal_code" />
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
        </div>

    </form>
    <div class="flex items-center gap-4 mt-5 ">
        <x-primary-button type="submit" form="profile-form" >{{ __('Save') }}</x-primary-button>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">{{ __('Saved.') }}</p>
        @endif
    </div>
</section>