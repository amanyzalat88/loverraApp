<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        'use strict';
        window.settings = {
            csrfToken: "{{ csrf_token() }}",
            logged_in_user: "{{ session('slack') }}",
            access_token: "{{ session('access_token') }}",
            logged_in: "{{ (session('slack') != '')?1:0 }}"
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_32_32.png') }}">
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}"> 
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    @stack('styles')
    <title>Loverra </title>
</head>