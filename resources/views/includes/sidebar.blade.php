 <div class="page-sidebar navbar-collapse collapse">
 <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
    <li class="sidebar-toggler-wrapper hide">
        <div class="sidebar-toggler">
            <span></span>
        </div>
    </li>
    <li class="nav-item start active open">
        <a href="{{url('admin')}}" class="nav-link nav-toggle">
            <i class="icon-home"></i>
            <span class="title">Dashboard</span>
            <span class="selected"></span>
            <span class="arrow open"></span>
        </a>
    </li>
    <li class="nav-item start open">

        <a href="{{url('events')}}" id="visitwebsite" class="nav-link nav-toggle">

            <i class="icon-home"></i>
            <span class="title">visit website</span>
            <span class="selected"></span>
            <span class="arrow open"></span>
        </a>
    </li>
    <li class="heading">
        <h3 class="uppercase">Features</h3>
    </li>
    <li class="nav-item">
        <a href="{{url('admin/users')}}" class="nav-link">
            <i class="icon-user"></i>
            <span class="title">Website Member</span>
            <span class="arrow"></span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{url('admin/admins')}}" class="nav-link">
            <i class="icon-diamond"></i>
            <span class="title">Admin User</span>
            <span class="arrow"></span>
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{url('admin/notifications')}}" class="nav-link nav-toggle">
            <i class="icon-info"></i>
            <span class="title">Notification Management</span>
            <span class="arrow"></span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{url('admin/emailtemplates')}}" class="nav-link">
            <i class="icon-envelope"></i>
            <span class="title">Email Templates</span>
            <span class="arrow"></span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{url('admin/events')}}" class="nav-link">
            <i class="icon-briefcase"></i>
            <span class="title">Event Management</span>
            <span class="arrow"></span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{url('admin/stories')}}" class="nav-link">
            <i class="icon-briefcase"></i>
            <span class="title">Story Management</span>
            <span class="arrow"></span>
        </a>
    </li>
    
    <li class="heading">
        <h3 class="uppercase">Admin Settings</h3>
    </li>
    
    <li class="nav-item">
        <a href="{{url('admin/settings')}}">
            <span class="title">Settings</span>
            <span class="arrow"></span>
        </a>
    </li>
</ul>