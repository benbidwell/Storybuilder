<!-- BEGIN HEADER INNER -->
<div class="page-header-inner ">
<!-- BEGIN LOGO -->
<div class="page-logo">
    <a href="{{url('admin')}}">
        <!-- <img src="../assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" /> --> <h3>Clip Crowd</h3></a>
    <div class="menu-toggler sidebar-toggler">
        <span></span>
    </div>
</div>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    <span></span>
</a>
<!-- END RESPONSIVE MENU TOGGLER -->
<!-- BEGIN TOP NAVIGATION MENU -->
<div class="top-menu">
    <ul class="nav navbar-nav pull-right">
        <li class="dropdown dropdown-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                @if(Auth::user()->profile_picture == '')
                <img alt="" class="img-circle" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" />
                @else
                <img alt="" class="img-circle" src="{{ asset('profile_pictures')}}/{{Auth::user()->profile_picture}}" />
                @endif
                <span class="username username-hide-on-mobile"> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-default">
                <li>
                    <a href="{{url('admin/settings')}}">
                        <i class="icon-user"></i> Settings </a>
                </li>
                <li>
                    <a href="{{url('admin/change-password')}}">
                        <i class="icon-user"></i>Change Password </a>
                </li>
                <li class="divider"> </li>
                <li>
                    <a class="logout" href="{{ url('admin/logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();"><i class="icon-key"></i>Logout
                    </a></li>
                    <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
            </ul>
        </li>
    </ul>
</div>
<!-- END TOP NAVIGATION MENU -->
</div>
<!-- END HEADER INNER -->           