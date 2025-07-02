<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Website resmi Himpunan Mahasiswa Jurusan Teknik Kimia (HMJTK) Politeknik Negeri Bandung. Menyajikan informasi terkini seputar kegiatan, berita, dan program kerja HMJTK Polban.">
    <meta name="keywords"
        content="HMJTK, HMJTK POLBAN, Himpunan Mahasiswa Jurusan Teknik Kimia, Polban, Politeknik Negeri Bandung, Teknik Kimia Polban">
    <meta name="author" content="HMJTK POLBAN">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title"
        content="HMJTK POLBAN - Himpunan Mahasiswa Jurusan Teknik Kimia Politeknik Negeri Bandung">
    <meta property="og:description"
        content="Website resmi Himpunan Mahasiswa Jurusan Teknik Kimia (HMJTK) Politeknik Negeri Bandung">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    @if ($settings = \App\Models\Setting::first())
        <meta property="og:image" content="{{ asset('storage/' . $settings->logo) }}">
        <link rel="icon" href="{{ asset('storage/' . $settings->logo) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/' . $settings->logo) }}" type="image/x-icon">
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
    @endphp

    @if ($manifest)
        <!-- Load built files -->
        <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
        @foreach ($manifest['resources/js/app.js']['css'] as $css)
            <link rel="stylesheet" href="{{ asset('build/' . $css) }}">
        @endforeach
    @endif
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
