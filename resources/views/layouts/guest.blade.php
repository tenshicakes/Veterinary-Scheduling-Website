<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css') 
    @livewireStyles
</head>
<body class="bg-background">
    {{ $slot }}
    @livewireScripts
</body>
</html>