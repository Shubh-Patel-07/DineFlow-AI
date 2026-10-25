<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DineFlow AI POS')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --canvas: #FBF9F8;
            --surface: #FFFFFF;
            --panel: #FCFCFC;
            --brand: #9F1239;
            --hairline: #ECE6E4;
            --text-primary: #111827;
            --text-muted: #6B7280;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; }
        body {
            font-family: 'Inter', sans-serif;
            background: #FBF9F8;
            color: #111827;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
    @yield('styles')
</head>
<body class="bg-[#FBF9F8] text-[#111827]">
    @yield('content')
</body>
</html>
