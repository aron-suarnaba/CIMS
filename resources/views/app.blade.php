<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!--

                                                            Author: Aron Kyle D. Suarnaba (System Analyst Programmer)
                                                            Year Published: 2025-2026
                                                            Description: CIMS is the unified system created for the Printwell IT
                                                                         Department to allow manage, monitor, and maintain IT
                                                                         inventory asset, network, system resources, server configuration,
                                                                         credentials, etc., without the need of multiple system to handle
                                                                         each of one of this.

                                                            NOTE: FOR THE FUTURE SYSTEM ANALYST PROGRAMMER, PLEASE DON'T TAKE ANY CREDITS FOR
                                                                  THIS SYSTEM, I AM ASKING POLITELY TO CONSIDER MY EFFORT IN THE DEVELOPMENT
                                                                  OF THIS SYSTEM. THANK YOU.

    -->

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'CIMS') }}</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
</head>

<body class="sidebar-expand-lg sidebar-mini layout-fixed">
    @inertia
</body>

</html>
