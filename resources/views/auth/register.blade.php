<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
        <p class="text-gray-600">Join millions of traders on Elite Forex Pro</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input id="name" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input id="email" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Enter your email address">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password') border-red-500 @enderror" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="Create a strong password">
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with letters and numbers</p>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <input id="password_confirmation" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password_confirmation') border-red-500 @enderror" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your password">
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
            <input id="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1" required>
            <label for="terms" class="ml-2 text-sm text-gray-600">
                I agree to the 
                <a href="#" class="text-blue-600 hover:text-blue-500 transition-colors">Terms of Service</a> 
                and 
                <a href="#" class="text-blue-600 hover:text-blue-500 transition-colors">Privacy Policy</a>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all transform hover:scale-[1.02]">
            Create Account
        </button>

        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium transition-colors">
                    Sign in here
                </a>
            </p>
        </div>
    </form>

    <!-- Security Notice -->
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Security First</h4>
                <p class="text-sm text-blue-700 mt-1">Your account will be protected with bank-level security and all deposits require admin approval.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
