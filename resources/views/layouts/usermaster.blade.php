<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @livewireStyles
</head>
<body>
    <header>
        
    </header>

    <h1>This is the master page for users.</h1>
    
    <main>
        {{ $slot }} <!-- Livewire injects home.blade.php here -->
    </main>
    @livewireScripts
</body>
</html>