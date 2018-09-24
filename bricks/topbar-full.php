<div class="topbar">
    <div class="float-right">
        <?php 
            require_once('/var/simplesamlphp/lib/_autoload.php');
            $as = new SimpleSAML_Auth_Simple('default-sp');
            if (!$as->isAuthenticated()) {
                echo '<a class="main-btn" href="/user/auth"><i class="fas fa-user-circle"></i> KENT LOGIN</a><a class="main-btn  login-btn" href="/user/auth"><i class="fas fa-user-circle"></i> GUEST LOGIN</a>';
            } else {
                echo '<a class="main-btn" href="/dashboard"><i class="fas fa-tachometer-alt"></i> DASHBOARD</a><a class="main-btn login-btn" href="/user/logout"><i class="fas fa-sign-out-alt"></i>LOGOUT</a>';
            }
        ?><!--<a class="main-btn burger-menu">Menu<i class="fas fa-bars"></i></a>-->
    </div>
</div>