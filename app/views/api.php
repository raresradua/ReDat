<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReDat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" href="css/main.css">
</head>

<body class = "index-body">

<?php
include("../templates/navbar.php");
?>
<h1> API Documentation </h1>
<section class="main-section">
    <section class="requests">
        <h2>Requests</h2>
        <div class="api-text">
            <p>
                You can use the <strong>API</strong> to obtain information/statistics about subreddits or a user's activity in a subreddits.
            </p>
            <p>
                In order to do so, you have to send a <strong>GET</strong> request to the subreddit endpoint, followed (optional) by the user name.
            </p>
            <p>
                The server will respond with a <strong>JSON</strong> containing the requested information.
            </p>
        </div>
        <div class="snippet">
            <pre class="line-numbers">
                <code class="language-python">
                    import requests


                    URL = "https://re-dat.herokuapp.com/api/r/soccer/skyehigh123"
                    r = requests.get(url = URL)

                    data = r.json()    # response
                </code>
            </pre>
        </div>
    </section>

</section>

<h1>

</h1>

<?php
include("../templates/footer.php");
?>

</body>
</html>