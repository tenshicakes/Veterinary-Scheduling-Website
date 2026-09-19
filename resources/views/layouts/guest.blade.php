<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css') 
    @livewireStyles
</head>
<body class="bg-background">
    {{ $slot }}
    @livewireScripts
</body>
</html>