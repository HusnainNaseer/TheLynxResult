
   <head>
        
        <meta charset="utf-8" />
        <title>
            The Lynx School Results | 
            {{ auth()->check() && auth()->user()->branch_name ? auth()->user()->name : '' }}
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesdesign" name="author" />
            <meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .heads{
        font-weight: bolder;
    }
</style>
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/lynx_logo.png') }}">

        <!-- jquery.vectormap css -->
        <link href="{{ asset('assets/auth/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="{{asset('assets/auth/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="{{asset('assets/auth/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />  

        <!-- Bootstrap Css -->
        <link href="{{asset('assets/auth/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('assets/auth/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('assets/auth/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
         <script>
    window.Laravel = {
        assetBase: "{{ asset('') }}"
    };
</script>

    </head>
  