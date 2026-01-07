@extends('layouts.auth')

@section('title', 'Lupa Password - Management Project')

@section('content')
<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden backdrop-blur-sm bg-opacity-95">
        <!-- Header -->
        <div class="bg-white px-8 py-10 text-center border-b border-gray-200">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-key text-5xl text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Lupa Password?</h2>
            <p class="text-gray-600 text-sm">Masukkan email Anda untuk menerima link reset password</p>
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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
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
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                Sudah ingat password Anda? 
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
