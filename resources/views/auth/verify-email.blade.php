<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-background min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md mx-auto w-full p-8 bg-white rounded-xl shadow-sm border border-gray-200 text-center">
        <svg class="mx-auto h-16 w-16 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h2 class="mt-4 text-2xl font-extrabold text-blue">Verify Your Email Address</h2>
        <p class="mt-2 text-gray-600">A verification link has been sent to your email address. Please click the link to verify your account.</p>
        <p class="mt-2 text-sm text-gray-500">If you didn't receive the email, click the button below to resend it.</p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full bg-blue text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition shadow-md">
                Resend Verification Email
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-500">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-blue hover:underline">
                Log out
            </a>
        </p>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</body>
</html>