<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css') 
    @livewireStyles
    <script>
        
        (function() {
            var params = new URLSearchParams(window.location.search);
            if (params.get('logout') === '1') {
                history.replaceState(null, '', '/');
                history.pushState(null, '', '/');
                window.addEventListener('popstate', function() {
                    history.pushState(null, '', '/');
                });
            }
        })();
    </script>
</head>
<body class="bg-background">
    {{ $slot }}
    @livewireScripts
</body>
</html>