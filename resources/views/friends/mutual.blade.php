@extends('layouts.app')
@section('title', 'Mutual Allies with ' . $user->name . ' - Akatsuki Devs')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-4 sm:py-8 relative overflow-hidden">
    <!-- Floating Clouds and Kunai Pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><path fill=\"%23FF6B35\" d=\"M20,40 Q30,30 40,40 T60,40 T80,40\" stroke=\"none\"/><path fill=\"%23D32F2F\" d=\"M30,50 L50,30 L70,50 L50,70 Z\" stroke=\"none\"/><circle fill=\"%23FF8C42\" cx=\"50\" cy=\"50\" r=\"8\"/></svg>'); background-repeat: repeat; background-size: 80px;"></div>
    </div>
    
    <!-- Animated Floating Elements -->
    <div class="absolute top-20 left-10 opacity-30 animate-float-slow pointer-events-none">
        <i class="fas fa-handshake text-6xl text-orange-400"></i>
    </div>
    <div class="absolute bottom-32 right-10 opacity-30 animate-float-medium pointer-events-none">
        <i class="fas fa-users text-6xl text-red-400"></i>
    </div>
    <div class="absolute top-1/3 right-20 opacity-20 animate-float-fast pointer-events-none">
        <i class="fas fa-star text-4xl text-orange-500"></i>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 animate-bounce-slow pointer-events-none z-20">
        <div class="flex flex-col items-center">
            <span class="text-xs text-orange-500 mb-1 font-medium">Scroll Down</span>
            <i class="fas fa-chevron-down text-orange-500"></i>
        </div>
    </div>
    
    <div class="max-w-6xl mx-auto px-3 sm:px-4 relative z-10">
        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8 animate-fade-in-down">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="relative animate-spin-slow">
                    <i class="fas fa-handshake text-orange-500 text-2xl sm:text-3xl"></i>
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-orange-500 via-red-500 to-orange-500 bg-clip-text text-transparent animate-gradient-x" style="font-family: 'Poppins', 'Inter', sans-serif;">
                    Mutual Allies with {{ $user->name }}
                </h1>
            </div>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-2 animate-fade-in-up font-medium">
                <i class="fas fa-scroll text-orange-500 mr-1"></i>
                Allies you both share in your ninja journey
            </p>
        </div>

        <!-- Mutual Count Card -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-orange-200/50 dark:border-orange-800/50 p-6 mb-6 sm:mb-8 text-center transform hover:scale-105 transition-all duration-300 animate-fade-in-up delay-100">
            <div class="flex items-center justify-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-2xl flex items-center justify-center animate-pulse-slow">
                    <i class="fas fa-handshake text-orange-600 dark:text-orange-400 text-2xl"></i>
                </div>
                <div>
                    <p class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent animate-count-up" style="font-family: 'Poppins', monospace;">
                        {{ $mutualCount }}
                    </p>
                    <p class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-1">
                        <i class="fas fa-star text-orange-500 mr-1 text-xs"></i>
                        Mutual Allies
                    </p>
                </div>
            </div>
        </div>

        <!-- Mutual Friends Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @forelse($mutualFriends as $friend)
                <div class="group bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg border border-orange-200/50 dark:border-orange-800/50 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden animate-fade-in-up">
                    <!-- Gradient Border Top -->
                    <div class="h-1 bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    
                    <div class="p-5 sm:p-6">
                        <!-- Friend Info -->
                        <div class="flex items-start space-x-3 sm:space-x-4 mb-4">
                            <a href="{{ route('user.profile', $friend->id) }}" class="flex-shrink-0 group relative">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-orange-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                                <img 
                                    src="{{ $friend->profile_photo_url }}"
                                    alt="{{ $friend->name }}"
                                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-3 border-white dark:border-gray-800 shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">
                                @if($friend->is_online)
                                    <span class="absolute bottom-1 right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full animate-pulse"></span>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('user.profile', $friend->id) }}" class="block">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                        {{ $friend->name }}
                                    </h3>
                                </a>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $friend->email }}
                                </p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <i class="fas fa-scroll text-orange-500 mr-1 text-xs"></i>
                                        {{ $friend->posts_count ?? 0 }} jutsu
                                    </span>
                                    @if($friend->is_online)
                                        <span class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                            <i class="fas fa-circle mr-1 text-xs animate-pulse"></i>
                                            Online
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2 mt-4">
                            <a href="{{ route('user.profile', $friend->id) }}"
                               class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white text-center py-2.5 px-3 rounded-xl transition-all duration-300 font-bold text-sm flex items-center justify-center group/btn relative overflow-hidden">
                                <div class="absolute inset-0 w-0 bg-white/20 transition-all duration-300 ease-out group-hover/btn:w-full"></div>
                                <i class="fas fa-user mr-2 relative z-10 group-hover/btn:animate-bounce"></i>
                                <span class="relative z-10">Profile</span>
                            </a>
                            <a href="{{ route('chat.index') }}?user={{ $friend->id }}"
                               class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white text-center py-2.5 px-3 rounded-xl transition-all duration-300 font-bold text-sm flex items-center justify-center group/msg relative overflow-hidden">
                                <div class="absolute inset-0 w-0 bg-white/20 transition-all duration-300 ease-out group-hover/msg:w-full"></div>
                                <i class="fas fa-comment mr-2 relative z-10 group-hover/msg:animate-bounce"></i>
                                <span class="relative z-10">Message</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-orange-200/50 dark:border-orange-800/50 p-8 sm:p-12 text-center transform transition-all duration-300 hover:scale-105 animate-fade-in-up">
                    <div class="relative inline-block">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-full blur-2xl opacity-50 animate-ping"></div>
                        <div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-full flex items-center justify-center animate-float">
                            <i class="fas fa-handshake text-2xl sm:text-3xl text-orange-500 dark:text-orange-400"></i>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-extrabold bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent mb-2 sm:mb-3 animate-gradient-x">
                        No Mutual Allies
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 sm:mb-6 max-w-md mx-auto font-medium">
                        You and {{ $user->name }} don't share any allies yet. Start connecting to build your ninja network!
                    </p>
                    <a href="{{ route('user.profile', $user->id) }}" 
                       class="group relative inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl overflow-hidden">
                        <div class="absolute inset-0 w-0 bg-white/20 transition-all duration-300 ease-out group-hover:w-full"></div>
                        <i class="fas fa-arrow-left mr-2 relative z-10 group-hover:animate-bounce"></i>
                        <span class="relative z-10">Back to Profile</span>
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Back Button -->
        @if($mutualFriends->count() > 0)
            <div class="mt-8 sm:mt-10 text-center animate-fade-in-up delay-300">
                <a href="{{ route('user.profile', $user->id) }}" 
                   class="group relative inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl overflow-hidden">
                    <div class="absolute inset-0 w-0 bg-white/20 transition-all duration-300 ease-out group-hover:w-full"></div>
                    <i class="fas fa-arrow-left mr-2 relative z-10 group-hover:animate-bounce"></i>
                    <span class="relative z-10">Back to {{ $user->name }}'s Profile</span>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap');
    
    body { font-family: 'Inter', system-ui, sans-serif; }
    h1, h2, h3, .font-heading { font-family: 'Poppins', 'Inter', sans-serif; letter-spacing: -0.02em; }
    
    @keyframes fade-in-down {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    @keyframes float-medium {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(-5deg); }
    }
    @keyframes float-fast {
        0%, 100% { transform: translateY(0px) translateX(0px); }
        50% { transform: translateY(-10px) translateX(10px); }
    }
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0) translateX(-50%); }
        50% { transform: translateY(-10px) translateX(-50%); }
    }
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }
    @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes gradient-x { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    @keyframes count-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .animate-fade-in-down { animation: fade-in-down 0.6s ease-out; }
    .animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; opacity: 0; animation-fill-mode: forwards; }
    .animate-float-slow { animation: float-slow 6s ease-in-out infinite; }
    .animate-float-medium { animation: float-medium 8s ease-in-out infinite; }
    .animate-float-fast { animation: float-fast 4s ease-in-out infinite; }
    .animate-bounce-slow { animation: bounce-slow 2s ease-in-out infinite; }
    .animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }
    .animate-spin-slow { animation: spin-slow 10s linear infinite; }
    .animate-gradient-x { background-size: 200% auto; animation: gradient-x 3s linear infinite; }
    .animate-count-up { animation: count-up 0.6s ease-out; }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #FF6B35, #D32F2F); border-radius: 10px; }
    .dark ::-webkit-scrollbar-track { background: #1f2937; }
</style>
@endsection