<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReDat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" >
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="../../public/css/main.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>

<body class = "index-body">

<?php
    include("../templates/navbar.php");
?>

<main id="display">
    <section class="main-section">
        <div id="subreddit">
            <div>
                <p><b>Title of subreddit:</b> <?php echo $data['about']->data->title?></p>
                <p><b>Description:</b> <?php echo $data['about']->data->public_description?></p>
            </div>
            <div>
                <p><b>Subscribers:</b> <?php echo $data['about']->data->subscribers?></p>
                <p><b>Active users:</b> <?php echo $data['about']->data->active_user_count?></p>
            </div>
        </div>

        <div id="subreddit">
            <div>
                <p><b>Total number of upvotes today: </b> <?php echo $data['todayStatistics']['upvotes']?></p>
                <p><b>Total number of comments today: </b> <?php echo $data['todayStatistics']['comments']?></p>
                <p><b>Total number of posts today: </b> <?php echo $data['todayStatistics']['posts']?></p>
            </div>
            <div>
                <p><b>Posts per Subscriber today: </b> <?php echo number_format(($data['about']->data->subscribers != null ? ($data['todayStatistics']['posts'] / $data['about']->data->subscribers):0), 8) . "%";?></p>
                <p><b>Comments per Subscriber today: </b><?php echo number_format(($data['about']->data->subscribers != null ? ($data['todayStatistics']['comments'] / $data['about']->data->subscribers) : 0), 8) . "%";?></p>
            </div>
        </div>

        <section class="top-posts">
            <div id="chooseTopPostsOption">
                <b>
                    Top Posts
                </b>
                &nbsp;
                <select name="ChooseOption" id="topPosts" onchange = updateTopPosts(this.value)>
                    <option value="today">Today</option>
                    <option value="week">This week</option>
                    <option value="month">This month</option>
                    <option value="year">This year</option>
                </select>
            </div>
        </section>
        <div class="topPostsTable">
            <table id="tableTopPosts">
                <thead>
                <tr>
                    <th>Upvotes</th>
                    <th>Comments</th>
                    <th>Post</th>
                    <th>Posted by</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    for($i = 0; $i < $data['posts']->data->dist; $i++){
                        echo "<tr>";
                            echo "<td>";
                                echo $data['posts']->data->children[$i]->data->score;
                            echo "</td>";
                            echo "<td>";
                                echo $data['posts']->data->children[$i]->data->num_comments;
                            echo "</td>";
                            echo "<td>";
                                echo "<a href= http://www.reddit.com/" . $data['posts']->data->children[$i]->data->permalink . " target=\"_blank\">" . $data['posts']->data->children[$i]->data->title . "</a>";
                            echo "</td>";
                            echo "<td>";
                                echo "<a href= ". "\"http://www.reddit.com/user/". $data['posts']->data->children[$i]->data->author . "\" target=\"_blank\">"."u/".$data['posts']->data->children[$i]->data->author ."</a>";
                            echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <script>

        async function updateTopPosts(timePeriod) {
          let submissions = await fetch(`https://www.reddit.com/r/${location.pathname.split("/").pop()}/top.json?t=${timePeriod}`).then(r => r.json()).then(j => j.data.children.map(c => c.data));
          if(submissions.length === 0 && timePeriod !== "year") {
            topPosts.value = "year";
            updateTopPosts("year");
            return; 
          }
          document.querySelector("#tableTopPosts").innerHTML = `
                <thead>
                <tr>
                    <th>Upvotes</th>
                    <th>Comments</th>
                    <th>Post</th>
                    <th>Posted by</th>
                </tr>
                </thead>
                <tbody>`
                + submissions.map((s,i) => `
                <tr><td>${s.score}</td><td>${s.num_comments}</td> <td><a href="https://www.reddit.com${s.permalink}" target=\"_blank\">${s.title}</a></td> <td><a href="https://www.reddit.com/u/${s.author}">/u/${s.author} </a></td></tr>`).join('') +`</tbody>` 
          ;
        }
        
        </script>
        <h1> Statistics </h1>
        <div class="graphs">
        <div class="statistics">
           <div id="comments"></div>
           <div id="posts"></div>
        </div>    
           <script>
               var x_set = <?php echo json_encode($data['dataset']['x']);?>; 
               var y_set = <?php echo json_encode($data['dataset']['y']);?>;
               var data =[
                {
                    x: x_set,
                    y: y_set,
                    type: 'bar'
                }
               ];
               var layout = {
                    title : 'Number of comments per Top Post in a day',
                    paper_bgcolor : 'rgba(0, 0, 0, 0)',
                    plot_bgcolor : 'rgba(165, 165, 141, 0.9)'
               };
               Plotly.newPlot('comments', data, layout);
           </script>

           <script>
                var x_set = <?php echo json_encode($data['datasetPostsDayMonth']['x']);?>;
                var y_set = <?php echo json_encode($data['datasetPostsDayMonth']['y']);?>;

                var data =[
                    {
                        x: x_set,
                        y: y_set,
                        type: 'scatter'
                    }
                ];
                var layout = {
                    title : 'Posts per day in a month',
                    paper_bgcolor : 'rgba(0, 0, 0, 0)',
                    plot_bgcolor : 'rgba(165, 165, 141, 0.9)'
                };
                Plotly.newPlot('posts', data, layout);
           </script>
           <!-- <script>
                    var x_set = <?php //echo json_encode($data['dataset']['x']);?>;
                    var y_set = <?php //echo json_encode($data['dataset']['y']);?>;
	                TESTER = document.getElementById('comments');
	                Plotly.newPlot( TESTER, [{
	                x: x_set,
	                y: y_set}], {
	                margin: { t: 0 } } );
                    Plotly.BUILD;
            </script> -->


            <div class="graphsFilters">
                <div class="downloadAs">
                    Download as: <span><button class="chooseBtn">SVG</button></span> / <span><button class="chooseBtn">CSV</button></span> <span><button class="dwnldBtn"><span class="material-icons">file_download</span></button></span>
                </div>
                <div class="filters">
                    Filter graph by: <span>
                    <select name="ChooseOption" id="filter">
                        <option value="posts">Posts</option>
                        <option value="time">Time</option>
                        <option value="usertime">Usertime</option>
                </select>
            </span>
                </div>
                <div class="chooseUsers">
                    <input type="text" id="searchUsers" onkeyup="searchUsers()" onclick="document.getElementById('userList').style.display = 'block';" placeholder="Search for names.." title="Type in a name">
                    <ul id="userList" style="display:none" >
                        <li><label for="01"><input type="checkbox" name="" id="01" value="01">User01</label></li>
                        <li><label for="02"><input type="checkbox" name="" id="02" value="02">User02</label></li>
                        <li><label for="03"><input type="checkbox" name="" id="03" value="03">User03</label></li>
                        <li><label for="04"><input type="checkbox" name="" id="04" value="04">User04</label></li>
                        <li><label for="05"><input type="checkbox" name="" id="05" value="05">User05</label></li>
                        <li><label for="06"><input type="checkbox" name="" id="06" value="06">User06</label></li>
                        <li><label for="07"><input type="checkbox" name="" id="07" value="07">User07</label></li>
                        <li><label for="08"><input type="checkbox" name="" id="08" value="08">User08</label></li>
                        <li><label for="09"><input type="checkbox" name="" id="09" value="09">User09</label></li>
                        <li><label for="10"><input type="checkbox" name="" id="10" value="10">User10</label></li>
                    </ul>
                    <script>
                        function searchUsers() {
                            var input, filter, ul, li, la, i, txtValue;
                            input = document.getElementById("searchUsers");
                            filter = input.value.toUpperCase();
                            ul = document.getElementById("userList");
                            li = ul.getElementsByTagName("li");
                            for (i = 0; i < li.length; i++) {
                                la = li[i].getElementsByTagName("label")[0];
                                txtValue = la.textContent || la.innerText;
                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                    li[i].style.display = "";
                                } else {
                                    li[i].style.display = "none";
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>

        <section id="list-tables">
            <div id="tables">
                <h2>Moderators</h2>
                <table id="tablestyle">
                    <thead>
                    <tr>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($data["moderators"]->data->children as $value) {
                            echo "<tr>";
                            echo "<td>";
                            $val = "<a href='http://reddit.com/u/" . $value->name . "'>" . "u/".$value->name . "</a>";
                            echo $val;
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <div id="tables">
                <h2>2nd table</h2>
                <table id="tablestyle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Subreddit</th>
                        <th>Growth %</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>memes</td>
                        <td>23%</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>cats</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>cats</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>oops</td>
                        <td>20%</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>dogs</td>
                        <td>10%</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="tables">
                <h2>3rd table</h2>
                <table id="tablestyle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Subreddit</th>
                        <th>Growth %</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>memes</td>
                        <td>23%</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>cats</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>bats</td>
                        <td>10%</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </section>

</main>

<?php
    include("../templates/footer.php");
?>
    <script>
        if(window.location.pathname == "/monitor"){
            document.getElementById("display").style.display = "none";
        }
        else{
            document.getElementById("display").style.display = "";
        }
    </script>
</body>
</html>
