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
include ("../templates/navbar.php");
?>
<h1> API Documentation </h1>
<h1> <u>NOTE: This is not final by any means </u></h1>
<section class="requests">
    <h2>Requests</h2>
    <div class="api-text">
        <p>
            You can use our <strong>API</strong> to obtain information/statistics about users in different subreddits.
        </p>
        <p>
            In order to do so, you have to send a <strong>HTTP</strong> request with the <strong>GET</strong> method and provide, in the parameters, a set
            of keys specifying the wanted query. (all the keys and values are listed bellow)
        </p>
        <p>
            The server will respond with a <strong>JSON</strong> containing the requested information.
        </p>
    </div>
    <div class="snippet">
        <pre class="line-numbers">
            <code class="language-python">
                import requests


                URL = "https://redat.com/api"
                params = {'subreddit': 'soccer', 'user': 'cr7', categories=["all"]}
                r = requests.get(url = URL, params = params)

                data = r.json()    # response
            </code>
        </pre>
    </div>
</section>

<section class="params">
    <h2>Structure of request JSON</h2>
    <div class="api-text">
        <p>The parameters you can send with the <strong>GET</strong> method are:</p>
        <ul>
            <li>
                <span class="key-value"><span class="key">"subreddit"</span>: <span class="value">string </span></span>
                <p>This field is mandatory and represents the subreddit for which stats are wanted.</p>
            </li>
            <li>
                <span class="key-value"><span class="key">"users"</span>: <span class="value">[string, ...] </span></span>
                <p>This field is not mandatory and represents the user from the subreddit for which stats are wanted. If this field is missing, general stats about the subreddit's users will be returned, like the mosts active users and such.</p>
            </li>
            <li>
                <span class="key-value"><span class="key">"years"</span>: <span class="value">{start: int, end: int} </span></span>
                <p>This field is not mandatory and represents the interval of time in which stats should be returned. Both start and end are optional, if missing, start will be replaced with the date of creation of the subreddit and end with the current date.</p>
            </li>
            <li>
                <span class="key-value"><span class="key"> "categories"</span>: <span class="value">["posts", "comments", "upvotes", "downvotes", "shares", "all"]</span></span>
                <p>This field is mandatory and represents the type of statistics to be returned. </p>
            </li>
        </ul>
    </div>
    <div class="snippet">
        <pre class="line-numbers">
            <code class="language-python">
                # Examples of GET method params
                params = {'subreddit': 'machinelearning', categories=["all"]}                                    # returns general stats about users in machinelearning subreddit
                params = {'subreddit': 'webdev', 'users': ['dummy1','dummy2'], categories=["posts", "comments"]} # returns stats about the users 'dummy1' and 'dummy2' from the 'webdev' subreddit, regarding posts and comments
                params = {'subreddit': 'reddevils', 'years': {start: 2018}, categories=["upvotes", "downvotes"]} # return stats about users in reddevils subreddit from 2018 to current date, regarding upvotes and downvotes
            </code>
        </pre>
    </div>

</section>

<section class="response">
    <h2>Structure of response JSON</h2>
    <div class="api-text">
        <p>The response received is heavily influenced by the parameters given in the <strong>GET</strong> method. </p>
        <p>If the users key is provided, the response contains a list of users with the requested categories as keys.</p>
    </div>
    <div class="snippet">
        <pre class="line-numbers">
            <code class="language-python">
                # General example of a response
                {
                    "subreddit": "webdev",
                    "years": [2009, 2021],
                    "users":
                        [
                            {
                                "user": "dummy1",
                                "posts": 100,
                                "comments": 1000
                            },
                            {
                                "user": "dummy2",
                                "posts": 1000,
                                "comments": 123
                            }
                        ]
                }

            </code>
        </pre>
    </div>
</section>

<h1>

</h1>

<?php
include ("../templates/footer.php");
?>

</body>
</html>