@extends('layouts.app')
@section('title', 'Welcome to Aplikasi Lab Darmajaya')
@section('content')

    {{-- 1. HERO SECTION --}}
    <section id="hero"
        class="min-h-[calc(100vh-4rem)] flex flex-col items-center justify-center text-center p-8 space-y-6 
        bg-gradient-to-br from-indigo-50 dark:from-gray-800 to-white dark:to-gray-900 border-b border-gray-200 dark:border-gray-800">

        <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            Selamat Datang di <br> Laboratorium <strong class="text-indigo-600 dark:text-indigo-400">Darmajaya</strong>
        </h1>

        <p class="text-xl max-w-4xl text-gray-600 dark:text-gray-400 px-4">
            Pusat pelayanan praktikum mahasiswa IIB Darmajaya, didukung 15 lab komputer canggih dan fasilitas memadai.
        </p>

        <a href="#about"
            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-300 ease-in-out transform hover:scale-105">
            Pelajari Lebih Lanjut
        </a>
    </section>

    {{-- 2. ABOUT (Visi & Misi) SECTION --}}
    <section id='about'
        class="min-h-0 w-full py-16 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-4xl text-center font-extrabold text-gray-900 dark:text-white mb-12">Tentang Kami</h2>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Card Visi --}}
            <div class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-xl border-t-4 border-indigo-500">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fa-solid fa-eye text-3xl text-indigo-500"></i>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Visi</h3>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-300 border-l-4 border-gray-100 dark:border-gray-700 pl-4">
                    Laboratorium komputer IIB Darmajaya menjadi <strong>laboratorium komputer yang mendukung pengembangan
                        ilmu pengetahuan, teknologi informasi dan bisnis.</strong>
                </p>
            </div>

            {{-- Card Misi --}}
            <div class="bg-white dark:bg-gray-900 p-8 rounded-xl shadow-xl border-t-4 border-indigo-500">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fa-solid fa-rocket text-3xl text-indigo-500"></i>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Misi</h3>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-300 border-l-4 border-gray-100 dark:border-gray-700 pl-4">
                    Menyelenggarakan kegiatan praktikum komputer, teknologi informasi dan bisnis yang selaras dengan
                    kebutuhan akademik dan mengembangkan keahlian dan keterampilan di bidang komputer bagi personalia yang
                    terlibat di Laboratorium Komputer IIB Darmajaya.
                </p>
            </div>
        </div>
    </section>

    {{-- 3. LAYANAN (FORM) & STATISTIK SECTION --}}
    <section id='services' class="mih-h-0 w-full py-16 px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl text-center font-extrabold text-gray-900 dark:text-white mb-12">Layanan & Formulir</h2>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">

            {{-- Card: G-FORM LAYANAN --}}
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-2xl hover:shadow-indigo-500/50 dark:hover:shadow-indigo-500/30 transition duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                <i class="fa-solid fa-clipboard-list text-4xl text-indigo-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">G-FORM LAYANAN</h3>
                <p class="text-md text-gray-600 dark:text-gray-300 mb-4">Isi form ini untuk melanjutkan layanan Laboratorium
                    Darmajaya.</p>
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSc--cgpnQ52FrNRn4hoBd-ZtNXL_gllYNmwTOMO0HjZN9PmyQ/viewform"
                    class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">
                    Isi Form Layanan
                </a>
            </div>

            {{-- Card: PENDAFTARAN ASISTEN --}}
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-2xl hover:shadow-indigo-500/50 dark:hover:shadow-indigo-500/30 transition duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                <i class="fa-solid fa-user-graduate text-4xl text-indigo-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">PENDAFTARAN ASISTEN</h3>
                <p class="text-md text-gray-600 dark:text-gray-300 mb-4">Daftar sebagai Asisten Laboratorium saat periode
                    telah dibuka.</p>
                <a href="https://s.id/asistenlabDJ"
                    class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">
                    Daftar Sekarang
                </a>
            </div>

            {{-- Card: PENILAIAN ASISTEN --}}
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-2xl hover:shadow-indigo-500/50 dark:hover:shadow-indigo-500/30 transition duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                <i class="fa-solid fa-star text-4xl text-indigo-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">PENILAIAN ASISTEN</h3>
                <p class="text-md text-gray-600 dark:text-gray-300 mb-4">Berikan penilaian Anda untuk Asisten Laboratorium.
                </p>
                <a href="https://docs.google.com/forms/d/e/1FAIpQLScUCoqcX5K4psroHo3mox0gMpo_SZV9leLlmKeeSKBZHYxhMA/viewform"
                    class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">
                    Beri Penilaian
                </a>
            </div>
        </div>

        {{-- Statistics Section --}}
        <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-8 shadow-inner max-w-6xl mx-auto">
            <h2 class="text-4xl text-center font-extrabold text-gray-900 dark:text-white mb-10">Statistik Laboratorium</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 text-center gap-8">

                <div
                    class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition duration-300">
                    <i class="fa-solid fa-user-group text-3xl text-indigo-600 dark:text-indigo-400 mb-2"></i>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">3</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Jumlah Laboran</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition duration-300">
                    <i class="fa-solid fa-user text-3xl text-indigo-600 dark:text-indigo-400 mb-2"></i>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">35</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Jumlah Asisten</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition duration-300">
                    <i class="fa-solid fa-building-columns text-3xl text-indigo-600 dark:text-indigo-400 mb-2"></i>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">15</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Jumlah Lab</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition duration-300">
                    <i class="fa-solid fa-desktop text-3xl text-indigo-600 dark:text-indigo-400 mb-2"></i>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">41</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Jumlah Komputer per Lab</p>
                </div>
            </div>
        </div>
    </section>
    <section id="team"
        class="min-h-0 w-full py-16 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-4xl text-center font-extrabold text-gray-900 dark:text-white mb-12">Team Laboratorium</h2>

        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <div
                class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-indigo-500/50">
                <div class="overflow-hidden">
                    <img src="{{ asset('Images/Homepage/Danang.jpg') }}" alt="DANANG"
                        class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">DANANG</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Koordinator Laboratorium</p>
                </div>
            </div>

            <div
                class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-indigo-500/50">
                <div class="overflow-hidden">
                    <img src="{{ asset('Images/Homepage/Meiliza.jpg') }}"  alt="MEILIZA"
                        class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">MEILIZA</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Staff Administrasi</p>
                </div>
            </div>

            <div
                class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-indigo-500/50">
                <div class="overflow-hidden">
                    <img src="{{ asset('Images/Homepage/Fauzan.jpg') }}"  alt="FAUZAN"
                        class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">FAUZAN</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Staff Laboran</p>
                </div>
            </div>

            <div
                class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-indigo-500/50">
                <div class="overflow-hidden">
                    <img src="{{ asset('Images/Homepage/Yuda.png') }}"  alt="YUDA"
                        class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">YUDA</p>
                    <p class="text-md text-gray-600 dark:text-gray-300 mt-1">Staff Laboran</p>
                </div>
            </div>

        </div>
    </section>
    <section id="contact"
        class="min-h-0 w-full py-16 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-4xl text-center font-extrabold text-gray-900 dark:text-white mb-12">Kontak Kami</h2>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 text-center">

            <!-- Address -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-indigo-500/50 transition duration-300">
                <i class="fa-solid fa-location-dot text-3xl text-indigo-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Alamat</h3>
                <p class="text-gray-600 dark:text-gray-300">Jl. ZA. Pagar Alam No.93, Gedong Meneng, Kec. Rajabasa, Kota Bandar Lampung, Lampung 35141
                </p>
            </div>

            <!-- Email -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-indigo-500/50 transition duration-300">
                <i class="fa-solid fa-envelope text-3xl text-indigo-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Email</h3>
                <p class="text-gray-600 dark:text-gray-300">lab@darmajaya.ac.id</p>
            </div>

            <!-- Phone -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-indigo-500/50 transition duration-300">
                <i class="fa-solid fa-phone text-3xl text-indigo-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Telepon</h3>
                <p class="text-gray-600 dark:text-gray-300">081253673243</p>
            </div>

        </div>
    </section>

@endsection
