 <nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
     <div class="sidebar-inner px-4 pt-3">
         <div
             class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
             <div class="d-flex align-items-center">
                 <div class="avatar-lg me-4">
                     <img src="{{ asset('frontEnd/images/fav-icon.png') }}"
                         class="card-img-top rounded-circle border-white" alt="Bonnie Green">
                 </div>
                 <div class="d-block">
                     <h2 class="h5 mb-3">Hi, Jane</h2>
                     <a href="javascript:void(0);" class="btn btn-secondary btn-sm d-inline-flex align-items-center">
                        
                         Sign Out
                     </a>
                 </div>
             </div>
             <div class="collapse-close d-md-none">
                 <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                     aria-controls="sidebarMenu" aria-expanded="true" aria-label="Toggle navigation">
                     
                 </a>
             </div>
         </div>
         <ul class="nav flex-column pt-3 pt-md-0">
             <li class="nav-item">
                 <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center">
                     <span class="sidebar-icon">
                         <img src="{{ asset('frontEnd/images/fav-icon.png') }}" height="20" width="20"
                             alt="Volt Logo">
                     </span>
                     <span class="mt-1 ms-1 sidebar-text">{{env('PROJECT_TITLE')}}</span>
                 </a>
             </li>
             @can('dashboard')
             <li class="nav-item  <?php echo $__env->yieldContent('title') == 'Dashboard' ? 'active' : ''; ?> ">
                 <a href="{{ route('dashboard') }}" class="nav-link">
                     <span class="sidebar-icon">
                         
                     </span>
                     <span class="sidebar-text">Dashboard</span>
                 </a>
             </li>
             @endcannot
             @can('tree_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Sponsor' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.tree') }}?type=Sponsor&typeId=1" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                    
                         </span>
                     <span class="sidebar-text">Tree</span>
                 </a>
             </li>
             @endcannot
             @can('adopt_tree_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Adopt' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.tree') }}?type=Adopt&typeId=2" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                     
                         </span>
                     <span class="sidebar-text">Add Adopt Tree</span>
                 </a>
             </li>
             @endcannot
             @can('adopted_tree_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Adopted' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.tree') }}?type=Adopted&typeId=2&adoptedStatus=1" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon"></span>
                     <span class="sidebar-text">Adopted Trees</span>
                 </a>
             </li>
             @endcannot
             @can('fund_tree_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'TreeFund' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.campaign') }}" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                        
                         </span>
                     <span class="sidebar-text">Green Fund(Campaign)</span>
                 </a>
             </li>
             @endcannot
             @can('blog_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Blogs' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.blogs') }}" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                     
                    </span>
                     <span class="sidebar-text">Blogs</span>
                 </a>
             </li>
             @endcannot
             @can('users_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Users' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.user') }}" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                     
                    </span>
                     <span class="sidebar-text">Users</span>
                 </a>
             </li>
             @endcannot
             @can('order_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Order' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.order') }}" class="nav-link ">
                     <i class="nav-icon far fa-envelope"></i>
                     <span class="sidebar-icon">
                    </span>
                     <span class="sidebar-text">Orders</span>
                 </a>
             </li>
             @endcannot
             
             @if(Auth::user()->role ==1)
             @can('employee_list')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Employee' ? 'active' : ''; ?>">
                <span class="nav-link d-flex justify-content-between align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#submenu-employee" aria-expanded="false"><span><span class="sidebar-icon">
                        
                </span><span class="sidebar-text">Manage Admins</span> </span><span class="link-arrow"></span></span>
                 <div class="multi-level collapse" role="list" id="submenu-employee" aria-expanded="false" style="">
                     <ul class="flex-column nav">
                        @foreach($rolesList as $role)
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.employee') }}?role={{$role->name}}&roleId={{$role->id}}"><span class="sidebar-text">{{$role->name ?? 'List'}}</span></a></li>
                        @endforeach
                     </ul>
                 </div>
             </li>
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Locations' ? 'active' : ''; ?>">
                <span class="nav-link d-flex justify-content-between align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#submenu-locations" aria-expanded="false"><span><span class="sidebar-icon">
                </span><span class="sidebar-text">Manage Locations</span> </span><span class="link-arrow">
                    
                         
                        </span></span>
                 <div class="multi-level collapse" role="list" id="submenu-locations" aria-expanded="false" style="">
                     <ul class="flex-column nav">
                     
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.states') }}"><span class="sidebar-text">States</span></a></li>
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.cities') }}"><span class="sidebar-text">Cities</span></a></li>
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.areas') }}"><span class="sidebar-text">Areas</span></a></li>
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.locations') }}"><span class="sidebar-text">Tree Locations</span></a></li>
                     </ul>
                 </div>
             </li>
             @endcannot
            
             @can('contact_us')
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'ContactUs' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.contact') }}" class="nav-link ">
                     <span class="sidebar-icon"> </span>
                     <span class="sidebar-text">Contact Us</span>
                 </a>
             </li>
             @endcannot
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Reports' ? 'active' : ''; ?>">
                <span class="nav-link d-flex justify-content-between align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#submenu-reports" aria-expanded="false"><span><span class="sidebar-icon">
                </span><span class="sidebar-text">Reports</span> </span><span class="link-arrow">
                   </span></span>
                 <div class="multi-level collapse" role="list" id="submenu-reports" aria-expanded="false" style="">
                     <ul class="flex-column nav">
                         <!-- <li class="nav-item"><a class="nav-link" href="{{ route('admin.report') }}"><span class="sidebar-text">Working Days</span></a></li> -->
                         <li class="nav-item"><a class="nav-link" href="{{ route('admin.report.orders') }}"><span class="sidebar-text">Orders</span></a></li>
                     </ul>
                 </div>
             </li>
             @endif
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'ChangePassword' ? 'active' : ''; ?>">
                 <a href="{{ route('admin.changePassword') }}" class="nav-link">
                     <span class="sidebar-icon"> </span>
                     <span class="sidebar-text">Change password</span>
                 </a>
             </li>

             @if(Auth::user()->role ==1)
             <li class="nav-item <?php echo $__env->yieldContent('title') == 'Roles' ? 'active' : ''; ?>">
                <span class="nav-link d-flex justify-content-between align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#submenu-role-manage" aria-expanded="false"><span><span class="sidebar-icon">
                        
                </span><span class="sidebar-text">Manage Roles</span> </span><span class="link-arrow"></span></span>
                 <div class="multi-level collapse" role="list" id="submenu-role-manage" aria-expanded="false" style="">
                     <ul class="flex-column nav">
                         <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}"><span class="sidebar-text">Roles</span></a></li>
                         <li class="nav-item"><a class="nav-link" href="{{ route('permissions.index') }}"><span class="sidebar-text">Permissions</span></a></li>
                     </ul>
                 </div>
             </li>
             @endif

         </ul>
     </div>
 </nav>add menu access tp this page , based on permissions  menu access manage , spatie/laravel-permission