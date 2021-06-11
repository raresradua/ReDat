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

<main>
    <script src="js/display.js"></script>
    <section class="main-section">
        <div id="subreddit">
            <div>
                <p><b>Title of subreddit:</b> <?php echo $data['about'][0]->title?></p>
                <p><b>Description:</b> <?php echo $data['about'][0]->public_description?></p>
            </div>
            <div>
                <p><b>Subscribers:</b> <?php echo $data['about'][0]->subscribers?></p>
                <p><b>Active users:</b> <?php echo $data['about'][0]->active_user_count?></p>
            </div>
        </div>

        <div id="subreddit">
            <div>
                <p><b>Total number of upvotes today: </b> <?php echo $data['about'][0]->today_upvotes?></p>
                <p><b>Total number of comments today: </b> <?php echo $data['about'][0]->today_comments?></p>
                <p><b>Total number of posts today: </b> <?php echo $data['about'][0]->today_posts?></p>
            </div>
            <div>
                <p><b>Posts per Subscriber today: </b> <?php echo number_format(($data['about'][0]->subscribers != null ? ($data['about'][0]->today_posts / $data['about'][0]->subscribers):0), 8) . "%";?></p>
                <p><b>Comments per Subscriber today: </b><?php echo number_format(($data['about'][0]->subscribers != null ? ($data['about'][0]->today_comments / $data['about'][0]->subscribers) : 0), 8) . "%";?></p>
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
                    for($i = 0; $i < $data['topPosts']->data->dist; $i++){
                        echo "<tr>";
                            echo "<td>";
                                echo $data['topPosts']->data->children[$i]->data->score;
                            echo "</td>";
                            echo "<td>";
                                echo $data['topPosts']->data->children[$i]->data->num_comments;
                            echo "</td>";
                            echo "<td>";
                                echo "<a href= http://www.reddit.com/" . $data['topPosts']->data->children[$i]->data->permalink . " target=\"_blank\">" . $data['topPosts']->data->children[$i]->data->title . "</a>";
                            echo "</td>";
                            echo "<td>";
                                echo "<a href= ". "\"http://www.reddit.com/user/". $data['topPosts']->data->children[$i]->data->author . "\" target=\"_blank\">"."u/".$data['topPosts']->data->children[$i]->data->author ."</a>";
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
                <div id="commonWords"></div>
            </div>
            <script>
                var x_set = <?php echo json_encode($data['datasetComments']['x']);?>;
                var y_set = <?php echo json_encode($data['datasetComments']['y']);?>;
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

                var buttons = {
                modeBarButtonsToRemove: ['toImage', 'sendDataToCloud'],
                modeBarButtonsToAdd:[{
                name: 'Download SVG format',
                icon: Plotly.Icons.camera,
                click: function(gd){
                Plotly.downloadImage(gd, {format: 'svg'})
            }
            }]
            };
                Plotly.newPlot('comments', data, layout, buttons);
        </script>

        <script>
            var x_set = <?php echo json_encode($data['datasetPosts']['x']);?>;
            var y_set = <?php echo json_encode($data['datasetPosts']['y']);?>;

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

            var buttons = {
                modeBarButtonsToRemove: ['toImage', 'sendDataToCloud'],
                modeBarButtonsToAdd:[{
                    name: 'Download SVG format',
                    icon: Plotly.Icons.camera,
                    click: function(gd){
                        Plotly.downloadImage(gd, {format: 'svg'})
                    }
                }]
            };
            Plotly.newPlot('posts', data, layout, buttons);
        </script>

        <script>
            var x_set = <?php echo json_encode(array_keys($data['commonWords']));?>;
            var y_set = <?php echo json_encode(array_values($data['commonWords']));?>;

            var data =[
                {
                    values: y_set,
                    labels: x_set,
                    type: 'pie'
                }
            ];
            var layout = {
                title : 'Posts per day in a month',
                paper_bgcolor : 'rgba(0, 0, 0, 0)',
                plot_bgcolor : 'rgba(165, 165, 141, 0.9)'
            };

            var buttons = {
                modeBarButtonsToRemove: ['toImage', 'sendDataToCloud'],
                modeBarButtonsToAdd:[{
                    name: 'Download SVG format',
                    icon: Plotly.Icons.camera,
                    click: function(gd){
                        Plotly.downloadImage(gd, {format: 'svg'})
                    }
                }]
            };
            Plotly.newPlot('commonWords', data, layout, buttons);
        </script>
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
                        foreach ($data["moderators"] as $value) {
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
                <h2>Users with most posts</h2>
                <table id="tablestyle">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Posts</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $arr_keys = array_keys($data["usersWithMostPosts"]);
                    foreach ($arr_keys as $arr_key) {
                        echo "<tr>";
                        echo "<td>";
                        $val = "<a href='http://reddit.com/u/" . $arr_key . "'>" . "u/".$arr_key . "</a>";
                        echo $val;
                        echo "</td>";
                        echo "<td>";
                        echo $data["usersWithMostPosts"][$arr_key];
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div id="tables">
                <h2>Users with most comments</h2>
                <table id="tablestyle">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Comments</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $arr_keys = array_keys($data["usersWithMostComments"]);
                    foreach ($arr_keys as $arr_key) {
                        echo "<tr>";
                        echo "<td>";
                        $val = "<a href='http://reddit.com/u/" . $arr_key . "'>" . "u/".$arr_key . "</a>";
                        echo $val;
                        echo "</td>";
                        echo "<td>";
                        echo $data["usersWithMostComments"][$arr_key];
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

    </section>

</main>

<?php
    include("../templates/footer.php");
?>
<script src="js/display-footer.js"></script>

</body>    
</html>
