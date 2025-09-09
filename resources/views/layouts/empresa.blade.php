@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
         :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
        
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 bg-blue-900">
            <h1 class="text-white text-xl font-bold">{{ auth()->user()->empresa->nome }}</h1>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-5 px-2">
            <a href="{{ route('empresa.dashboard', auth()->user()->empresa->slug) }}" 
               class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white {{ request()->routeIs('empresa.dashboard') ? 'bg-blue-900 text-white' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                </svg>
                Dashboard
            </a>
            
            <a href="{{ route('empresa.sepultamentos.index', auth()->user()->empresa->slug) }}" 
               class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white {{ request()->routeIs('empresa.sepultamentos.*') ? 'bg-blue-900 text-white' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Sepultamentos
            </a>
            
            <a href="{{ route('empresa.sepultamentos.create', auth()->user()->empresa->slug) }}" 
               class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white {{ request()->routeIs('empresa.sepultamentos.create') ? 'bg-blue-900 text-white' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Novo Sepultamento
            </a>
        </nav>
        
        <!-- User info -->
        <div class="absolute bottom-16 w-full px-2">
            <div class="bg-blue-900 rounded-lg p-3">
                <p class="text-blue-100 text-sm font-medium">{{ auth()->user()->name }}</p>
                <p class="text-blue-200 text-xs">{{ auth()->user()->email }}</p>
            </div>
        </div>
        
        <!-- Logout -->
        <div class="absolute bottom-0 w-full p-2">
            <form method="POST" action="{{ route('empresa.logout', auth()->user()->empresa->slug) }}">
                @csrf
                <button type="submit" class="w-full group flex items-center px-2 py-2 text-base font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white">
                    <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </div>
    
    <!-- Overlay for mobile -->
    <div class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden" 
         x-show="sidebarOpen" 
         x-cloak
         @click="sidebarOpen = false"></div>
    
    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top bar -->
        <header class="bg-white shadow-sm lg:static lg:overflow-y-visible">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative flex justify-between xl:grid xl:grid-cols-12 lg:gap-8">
                    <div class="flex md:absolute md:left-0 md:inset-y-0 lg:static xl:col-span-2">
                        <div class="flex-shrink-0 flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="min-w-0 flex-1 md:px-8 lg:px-0 xl:col-span-6">
                        <div class="flex items-center px-6 py-4 md:max-w-3xl md:mx-auto lg:max-w-none lg:mx-0 xl:px-0">
                            <h1 class="text-2xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center md:absolute md:right-0 md:inset-y-0 lg:hidden xl:col-span-4">
                        <!-- User menu for mobile -->
                        <div class="flex-shrink-0 relative ml-5">
                            <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page content -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    @yield('page-content')
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

