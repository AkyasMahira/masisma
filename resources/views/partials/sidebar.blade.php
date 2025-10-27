<div class="sidebar position-fixed d-flex flex-column p-3">
    <h4 class="text-center mb-4"><i class="bi bi-lightbulb"></i> MyApp</h4>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart me-2"></i> Reports
            </a>
        </li>
    </ul>

    <hr>
    <a href="" class="text-white text-decoration-none">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>
