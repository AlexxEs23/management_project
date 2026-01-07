@extends('layouts.auth')

@section('title', 'Login - Management Project')

@section('content')
<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden backdrop-blur-sm bg-opacity-95">
        <!-- Header -->
        <div class="bg-white px-8 py-10 text-center border-b border-gray-200">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-circle text-5xl text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back!</h2>
            <p class="text-gray-600 text-sm">Login untuk melanjutkan</p>
        </div>
        
        <!-- Body -->
        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-700 text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                        <div class="text-red-700 text-sm">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email Address
                    </label>
                    <input type="email" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           required 
                           autofocus>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-indigo-500 mr-2"></i>Password
                    </label>
                    <input type="password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan password"
                           required>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transform transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">OR</span>
                </div>
            </div>

            <!-- Info -->
            <div class="text-center bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Admin & Employee access only. Contact administrator for credentials.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-6">
        <p class="text-gray-600 text-sm">© 2025 Management Project. All rights reserved.</p>
    </div>
</div>
@endsection
