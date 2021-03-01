<ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>
    <li class="active">
        <a href="/dashboard">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>            
        </a>        
    </li>
    <li {!! Request::is('*users*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Users</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
        
            <li {!! Request::is('*allusers*') ? ' class="active"' : null !!}><a href="{{url('allusers')}}"><i class="fa fa-circle-o"></i> All User</a></li>
         
            <li {!! Request::is('*create_users*') ? ' class="active"' : null !!}><a href="{{url('create_users')}}"><i class="fa fa-circle-o"></i> New User</a></li>
        </ul>
    </li>
    <li {!! Request::is('students_*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Students</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
        
            <li {!! Request::is('students_all_students') ? ' class="active"' : null !!}><a href="{{url('students_all_students')}}"><i class="fa fa-circle-o"></i> All Students</a></li>
            <li {!! Request::is('*students_active_students*') ? ' class="active"' : null !!}><a href="{{url('students_active_students')}}"><i class="fa fa-circle-o"></i> Active Students</a></li>
            <li {!! Request::is('*students_batch_wise_student_page*') ? ' class="active"' : null !!}><a href="{{url('students_batch_wise_student_page')}}"><i class="fa fa-circle-o"></i> Batch wise Students</a></li>
            <li {!! Request::is('students_create_student') ? ' class="active"' : null !!}><a href="{{url('students_create_student')}}"><i class="fa fa-circle-o"></i>Add New Student</a></li>
            <li {!! Request::is('students_payment_*') ? ' class="active"' : null !!}>
                <a href="#">
                    <i class="fa fa-circle-o"></i> Payment Section
                </a>
                <ul class="treeview-menu">
                    <li {!! Request::is('students_payment_batch_student') ? ' class="active"' : null !!}>
                        <a href="{{url('students_payment_batch_student')}}">
                            <i class="fa fa-circle-o"></i> Batch Payment
                        </a>
                    </li>
                    <li {!! Request::is('students_payment_other') ? ' class="active"' : null !!}>
                        <a href="{{url('students_payment_other')}}">
                            <i class="fa fa-circle-o"></i> Other Payment
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li {!! Request::is('*teacher*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Teacher</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('*all_teachers*') ? ' class="active"' : null !!}><a href="{{url('all_teachers')}}"><i class="fa fa-circle-o"></i> All Teachers</a></li>
            <li {!! Request::is('*create_teacher*') ? ' class="active"' : null !!}><a href="{{url('create_teacher')}}"><i class="fa fa-circle-o"></i>Add New Teacher</a></li>
            <li {!! Request::is('*teacher_payment_all_batch*') ? ' class="active"' : null !!}><a href="{{url('teacher_payment_all_batch')}}"><i class="fa fa-circle-o"></i>Teacher Payment</a></li>
        </ul>
    </li>
    <li {!! Request::is('*subject*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Subjects</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('*all_subjects*') ? ' class="active"' : null !!}><a href="{{url('all_subjects')}}"><i class="fa fa-circle-o"></i> All Subjects</a></li>
            <li {!! Request::is('*create_subject*') ? ' class="active"' : null !!}><a href="{{url('create_subject')}}"><i class="fa fa-circle-o"></i>Add New Subject</a></li>
        </ul>
    </li>
    <li {!! Request::is('*grade*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Grades</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('*all_grades*') ? ' class="active"' : null !!}><a href="{{url('all_grades')}}"><i class="fa fa-circle-o"></i> All Grades</a></li>
            <li {!! Request::is('*create_grade*') ? ' class="active"' : null !!}><a href="{{url('create_grade')}}"><i class="fa fa-circle-o"></i>Add New Grade</a></li>
        </ul>
    </li>
    <li {!! Request::is('*school*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Schools</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('*all_schools*') ? ' class="active"' : null !!}><a href="{{url('all_schools')}}"><i class="fa fa-circle-o"></i> All Schools</a></li>
            <li {!! Request::is('*create_school*') ? ' class="active"' : null !!}><a href="{{url('create_school')}}"><i class="fa fa-circle-o"></i>Add New School</a></li>
        </ul>
    </li>
    <li {!! Request::is('*reporting*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Reporting</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('payment_reporting') ? ' class="active"' : null !!}><a href="{{url('payment_reporting')}}"><i class="fa fa-circle-o"></i> Batch Payment Reporting </a></li>
            <li {!! Request::is('other_payment_reporting') ? ' class="active"' : null !!}><a href="{{url('other_payment_reporting')}}"><i class="fa fa-circle-o"></i> Other's Payment Reporting </a></li>
        </ul>
    </li>
    <li {!! Request::is('*roles*') || Request::is('*permissions*') ? ' class="active treeview"' : ' class="treeview"' !!} class="treeview">
        <a href="#">
            <i class="fa fa-gears"></i>
            <span>Settings</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('*roles*') ? ' class="active"' : null !!}><a href="{{url('roles')}}"><i class="fa fa-circle-o"></i> Roles</a></li>
            <li {!! Request::is('*permissions*') ? ' class="active"' : null !!}><a href="{{url('permissions')}}"><i class="fa fa-circle-o"></i> Permission</a></li>
        </ul>
    </li>
</ul>
