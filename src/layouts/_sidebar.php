<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
      <img src="assets/images/tesda-logo.png" />
      <span class="brand-text">SRIS</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">Manage Student</li>
          <li class="nav-item">
            <a href="registrations.php" class="nav-link">
              <i class="nav-icon fa-solid fa-users"></i>
              <p>
                Registrations
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="add.php" class="nav-link">
              <i class="nav-icon fa-solid fa-user-plus"></i>
              <p>
                New Registration
              </p>
            </a>
          </li>
          <?php 
            $user_roles = get_user_roles($_SESSION['id'], 'names'); 
            if (count(array_intersect(['super_admin', 'administrator'], $user_roles)) > 0): 
          ?>
            <li class="nav-item">
              <a href="users.php" class="nav-link">
                <i class="nav-icon fa-solid fa-users-gear"></i>
                <p>Users</p>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>