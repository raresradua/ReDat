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
                <form id="searchForm" method="get">
                    <input id="searchInput" type="search" placeholder='<?php echo $data['current_subreddit'] ?>'
                           list="subreddits" oninput='onInput()'>
                    <datalist id="subreddits">
                        <?php
                        foreach($data['subreddits'] as $value)
                            echo '<option value="'. $value->subreddit .'">'
                        ?>
                    </datalist>
                </form>
                <script>
                    function onInput() {
                        var val = document.getElementById("searchInput").value;
                        var opts = document.getElementById('subreddits').childNodes;
                        for (var i = 0; i < opts.length; i++) {
                            if (opts[i].value === val) {
                                var form = document.getElementById("searchForm");
                                form.action = 'http://localhost/ReDat/monitor/' + val;
                                form.submit();
                                
                            }
                        }
                    }
                </script>
            </li>
            <li><a href="<?php echo URLROOT; ?>/api" class="nav-links">API</a></li>
            <li><a href="<?php echo URLROOT;?>/monitor/logout" class="log-out">Log out</a></li>
        </ul>
    </nav>
    <script src = "js/navbar.js"></script>
    <script src = "../../public/js/navbar.js"></script>
</div>