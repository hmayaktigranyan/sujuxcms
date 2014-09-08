<!DOCTYPE html>

<html lang="en">

    <head id="Starter-Site">

        <meta charset="UTF-8">

        <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>
            @section('title')
            Administration
            @show
        </title>

        <meta name="keywords" content="@yield('keywords')" />
        <meta name="author" content="@yield('author')" />
        <!-- Google will often use this as its description of your page/site. Make it good. -->
        <meta name="description" content="@yield('description')" />

        <!-- Speaking of Google, don't forget to set your site up: http://google.com/webmasters -->
        <meta name="google-site-verification" content="">

        <!-- Dublin Core Metadata : http://dublincore.org/ -->
        <meta name="DC.title" content="Project Name">
        <meta name="DC.subject" content="@yield('description')">
        <meta name="DC.creator" content="@yield('author')">

        <!--  Mobile Viewport Fix -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <base href="{{{ URL::to('/') }}}/" >
        <!-- This is the traditional favicon.
         - size: 16x16 or 32x32
         - transparency is OK
         - see wikipedia for info on browser support: http://mky.be/favicon/ -->
        <link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">

        <!-- iOS favicons. -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
        <link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">

        <!-- CSS -->
        <link rel="stylesheet" href="{{{asset('bootstrap/css/bootstrap.min.css')}}}">
        <link rel="stylesheet" href="{{{asset('bootstrap/css/bootstrap-theme.min.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/css/admin.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/css/wysihtml5/prettify.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/css/wysihtml5/bootstrap-wysihtml5.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/select2/select2.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/select2/select2-bootstrap.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/datepicker/css/datepicker.css')}}}">
        <link rel="stylesheet" href="{{{asset('assets/fancybox/jquery.fancybox-1.3.4.css')}}}" />
        <link rel="stylesheet" href="{{{asset('assets/bootstrapvalidator/dist/css/bootstrapValidator.min.css')}}}"/>
        <style>
            body {
                padding: 60px 0;
            }
        </style>

        @yield('styles')

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!-- Javascripts -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
        <script src="{{{asset('bootstrap/js/bootstrap.min.js')}}}"></script>
        <script src="{{{asset('assets/js/wysihtml5/wysihtml5-0.3.0.js')}}}"></script>
        <script src="{{{asset('assets/js/wysihtml5/bootstrap-wysihtml5.js')}}}"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
        <script src="{{{asset('assets/js/prettify.js')}}}"></script>
        <script src="{{{asset('assets/datepicker/js/bootstrap-datepicker.js')}}}"></script>
        <script src="{{{asset('assets/select2/select2.js')}}}"></script>
        <script src="{{{asset('assets/fancybox/jquery.fancybox-1.3.4.pack.js')}}}"></script>
        <script src="{{{asset('assets/bootstrapvalidator/dist/js/bootstrapValidator.min.js')}}}"></script>
        <script src="{{{asset('assets//tinymce/tinymce.min.js')}}}"></script>
        <script>
            var filemanager_access_key = "<?php echo md5("jdlfa900" . Config::get('app.key'));?>";
            <?php
            $baseUrl = URL::to('/');
            $urlParts = parse_url($baseUrl);
            ?>
                var base_url = "{{{ URL::to('/') }}}/";
            var external_filemanager_path = "<?php echo $urlParts['path']?>/filemanager/";
            </script>
        <script src="{{{asset('assets/js/admin.js')}}}"></script>


        @yield('scripts')
    </head>

    <body>
        <!-- Container -->
        <div class="container-fluid">
            <!-- Navbar -->
            <div class="navbar navbar-default navbar-inverse navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse navbar-ex1-collapse">
                        <ul class="nav navbar-nav">
                            <li{{{ (Request::is('admin') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin') }}}"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                            <li class="dropdown{{{ (Request::is('admin/object*') ? ' active' : '') }}}">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="glyphicon "></span> Objects <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($entities as $entity) {
                                        ?>
                                        <li{{{ (Request::is('admin/object/'.$entity->id) ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/object/'.$entity->id) }}}"><span class="glyphicon glyphicon-th-list"></span> <?php echo $entity->title ?> List</a></li>

                                    <?php }
                                    ?>
                                </ul>
                            </li>
                            <li class="dropdown{{{ (Request::is('admin/object/create*') ? ' active' : '') }}}">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" >
                                    <span class="glyphicon "></span>Add Object <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($entities as $entity) {
                                        ?>
                                        <li{{{ (Request::is('admin/object/create/'.$entity->id) ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/object/create/'.$entity->id) }}}"><span class="glyphicon glyphicon-plus"></span> Add <?php echo $entity->title ?></a></li>

                                    <?php }
                                    ?>
                                </ul>
                            </li>
                            <li class="dropdown{{{ (Request::is('admin/entity*') ? ' active' : '') }}}">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="{{{ URL::to('admin/entity') }}}">
                                    <span class="glyphicon "></span> Entities <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li{{{ (Request::is('admin/entity') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/entity') }}}"><span class="glyphicon glyphicon-th-list"></span> List</a></li>
                                    <li{{{ (Request::is('admin/entity/create') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/entity/create') }}}"><span class="glyphicon glyphicon-plus"></span> Add</a></li>
                                </ul>
                            </li><li class="dropdown{{{ (Request::is('admin/language*') ? ' active' : '') }}}">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="{{{ URL::to('admin/language') }}}">
                                    <span class="glyphicon "></span> Languages <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li{{{ (Request::is('admin/language') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/language') }}}"><span class="glyphicon glyphicon-th-list"></span> List</a></li>
                                    <li{{{ (Request::is('admin/language/create') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/language/create') }}}"><span class="glyphicon glyphicon-plus"></span> Add</a></li>
                                </ul>
                            </li>
                            <li class="dropdown{{{ (Request::is('admin/taxonomy*') ? ' active' : '') }}}">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="{{{ URL::to('admin/taxonomy') }}}">
                                    <span class="glyphicon "></span> Taxonomies <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li{{{ (Request::is('admin/taxonomy') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/taxonomy') }}}"><span class="glyphicon glyphicon-th-list"></span> List</a></li>
                                    <li{{{ (Request::is('admin/taxonomy/create') ? ' class="active"' : '') }}}><a href="{{{ URL::to('admin/taxonomy/create') }}}"><span class="glyphicon glyphicon-plus"></span> Add</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav pull-right">
                            <li><a href="{{{ URL::to('/') }}}">View Homepage</a></li>
                            <li class="divider-vertical"></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="glyphicon glyphicon-user"></span> {{{ Sentry::getUser()->username }}}	<span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{{ URL::to('admin/profile') }}}"><span class="glyphicon glyphicon-wrench"></span> Profile</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{{{ URL::to('user/logout') }}}"><span class="glyphicon glyphicon-share"></span> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ./ navbar -->

            <!-- Notifications -->
            {{ Notification::showAll() }}
            <!-- ./ notifications -->

            <!-- Content -->
            @yield('content')
            <!-- ./ content -->

            <!-- Footer -->
            <footer class="clearfix">
                @yield('footer')
            </footer>
            <!-- ./ Footer -->

        </div>
        <!-- ./ container -->



    </body>

</html>
