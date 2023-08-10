<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white-100 shadow">
    <div style="background: rgb(255 255 255 /90%);"
        class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $logo }}

        {{ $slot }}
    </div>
</div>
<style>
    body {
        background-size: cover;
        background-image: url({{ asset('kcnew/frontend/img/background.png') }});
    }
</style>
