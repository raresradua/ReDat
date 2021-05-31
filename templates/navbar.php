<div class="nav-box">
    <nav>
        <h1 id="logo"> <a class="logo-a" href="<?php echo URLROOT; ?>/monitor">ReDAT </a></h1>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="nav-menu">
            <li>
                <form action="/search/" method="get">
                    <input type="search" placeholder="/r/Subreddit">
                </form>
            </li>
            <li><a href="<?php echo URLROOT; ?>/api" class="nav-links">API</a></li>
            <li><a href="<?php echo URLROOT;?>/monitor/logout" class="log-out">Log out</a></li>
        </ul>
    </nav>
    <script src = "js/navbar.js"></script>
</div>