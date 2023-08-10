<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>



        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>
            <div class="mb-4 mt-3 text-sm text-gray-600">
                {{ __('Quên mật khẩu? Chỉ cần cho chúng tôi biết địa chỉ email của bạn và chúng tôi sẽ gửi email cho bạn liên kết đặt lại mật khẩu cho phép bạn chọn một mật khẩu mới.') }}
            </div>

            <div class="flex items-center justify-between mt-4 mr-3">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Trở về đăng nhập') }}
                </a>
                <x-button>
                    {{ __('Lấy lại mật khẩu') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
