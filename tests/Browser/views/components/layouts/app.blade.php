<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WireStrap Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/_ws/wirestrap.css">
    @livewireStyles
</head>
<body x-data>
    {{ $slot }}

    @livewireScripts
    <script src="/_ws/wirestrap.js"></script>
</body>
</html>
