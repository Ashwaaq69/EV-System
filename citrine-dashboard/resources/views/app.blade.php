<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @auth
        @if(auth()->user()->role === 'client')
            <!-- Vue icon ONLY for clients -->
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 128 128'%3E%3Cpath fill='%2342b883' d='M78.8,10L64,35.4L49.2,10H0l64,110l64-110H78.8z'/%3E%3Cpath fill='%2335495e' d='M78.8,10L64,35.4L49.2,10H25.6L64,76l38.4-66H78.8z'/%3E%3C/svg%3E">
        @endif
    @endauth
    <title>{{ config('app.name', 'Laravel') }}</title>
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>