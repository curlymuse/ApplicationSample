<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    @if (config('app.env') === 'local')
        <link rel="stylesheet" href="{{ url('/css/OpenSans.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ url('/css/font-awesome.min.css') }}" type="text/css">
    @else
        <link rel="stylesheet" href="{{ url('//fonts.googleapis.com/css?family=Open+Sans:300,400,600') }}" type="text/css">
        <link rel="stylesheet" href="{{ url('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css') }}" type="text/css">
    @endif

    <!-- CSS -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    @yield('scripts', '')

    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>;
    </script>
</head>
<body class="with-navbar">
    <div>
        <!-- Navigation -->
        @if (Auth::check())
            @include('spark::nav.blade.user')
        @else
            @include('spark::nav.guest')
        @endif

        <!-- Main Content -->
        @yield('content')

        <!-- JavaScript -->
        <script src="/js/app.js"></script>
    </div>
</body>
</html>
