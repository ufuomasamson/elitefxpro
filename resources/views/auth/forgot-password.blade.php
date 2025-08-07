<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
        <p class="text-gray-600">Enter your email to receive a password reset link</p>
    </div>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <p class="text-sm text-gray-600">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input id="email" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   placeholder="Enter your email address">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all transform hover:scale-[1.02]">
            Send Reset Link
        </button>

        <!-- Back to Login -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Remember your password? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium transition-colors">
                    Back to Sign In
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
