@extends('layouts.auth')

@section('title', 'Reset Password - Management Project')

@section('content')
<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden backdrop-blur-sm bg-opacity-95">
        <!-- Header -->
        <div class="bg-white px-8 py-10 text-center border-b border-gray-200">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-5xl text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h2>
            <p class="text-gray-600 text-sm">Masukkan password baru untuk akun Anda</p>
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

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div>
                    <label for="email_display" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email Address
                    </label>
                    <input type="email" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 cursor-not-allowed" 
                           id="email_display" 
                           value="{{ $email }}"
                           readonly>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-indigo-500 mr-2"></i>Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Minimal 8 karakter"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password', 'togglePasswordIcon')"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-indigo-500 mr-2"></i>Konfirmasi Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Ketik ulang password baru"
                               required>
                        <button type="button" 
                                onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="togglePasswordConfirmIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                Kembali ke halaman 
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
