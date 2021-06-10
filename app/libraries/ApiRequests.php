<?php


class ApiRequests
{
    private $token_type;
    private $access_token;

    public function __construct($token_type, $access_token)
    {
        $this->token_type = $token_type;
        $this->access_token = $access_token;
    }

    public function getSubreddits($where = "subscriber", $limit = 25, $after = null, $before = null)
    {
        $qAfter = (!empty($after)) ? "&after=" . $after : "";
        $qBefore = (!empty($before)) ? "&before=" . $before : "";

        $urlSubRel = sprintf("%s/subreddits/mine/$where?limit=%s%s%s",
            ENDPOINT_OAUTH,
            $where,
            $limit,
            $qAfter,
            $qBefore);

        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getSubredditPosts($subreddit, $when = "top", $time = "today"){
        $urlSubRel = sprintf("%s/r/%s/%s.json?t=%s",
        ENDPOINT_OAUTH,
        $subreddit,
        $when,
        $time
        );
        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getSubredditInfo($subreddit){
        $urlSubRel = sprintf("%s/r/%s/about.json",
        ENDPOINT_OAUTH,
        $subreddit    
        );
        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getNumberOfUpvotesPostsComments($subreddit){
        $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=today",
        ENDPOINT_OAUTH,
        $subreddit
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        $countUpvotes = 0;
        $countPosts = 0;
        $countComments = 0;
        
        while(!empty($data)){
            $after = $data->data->after;
            foreach($data->data->children as $child){
                $countUpvotes += $child->data->score;
                $countComments += $child->data->num_comments;
                $countPosts += 1;
            }
            $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=today&after=%s",
            ENDPOINT_OAUTH,
            $subreddit,
            $after
            );
            if($after == null)
                break;
            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token); 
        }
        
        $info = [
            "upvotes" => $countUpvotes,
            "comments" => $countComments,
            "posts" => $countPosts
        ];

        return $info;
    }

    public function getMostRecentPosts($subreddit, $size){
        $before = time();
        $oneMonthTime = 2678400;
        $posts = array();
        while (count($posts) < 1000) {
            $url = sprintf("%s/submission/?subreddit=%s&after=%d&before=%d&sort=desc&sort_type=created_utc&size=%d&fields=author,title,full_link,created_utc,full_text",
                PUSHSHIFT_API,
                $subreddit,
                $before - $oneMonthTime,
                $before,
                $size
            );
            $tmp = Request::runCurl($url);
            if (empty($tmp->data)){
                break;
            }
            $posts = array_merge($posts, $tmp->data);
            $before = end($posts)->created_utc;
        }

        return $posts;
    }

    public function getMostRecentComments($subreddit, $size){
        $before = time();
        $oneMonthTime = 2678400;
        $comments = array();
        while (count($comments) < 10000) {
            $url = sprintf("%s/comment/?subreddit=%s&after=%d&before=%d&sort=desc&sort_type=created_utc&size=%d&fields=author,title,full_link,created_utc,body",
                PUSHSHIFT_API,
                $subreddit,
                $before - $oneMonthTime,
                $before,
                $size
            );
            $tmp = Request::runCurl($url);
            if (empty($tmp->data)){
                break;
            }
            $comments = array_merge($comments, $tmp->data);
            $before = end($comments)->created_utc;
        }

        return $comments;
    }

    public function calculateUsersWithMostPosts($posts){
        $users = array();
        foreach($posts as $post){
            array_push($users, $post->author);
        }
        $values = array_count_values($users);
        arsort($values);
        return array_slice($values, 0, 10, true);
    }

    public function calculateUsersWithMostComments($comments){
        $users = array();
        foreach($comments as $comment){
            array_push($users, $comment->author);
        }
        $values = array_count_values($users);
        arsort($values);
        return array_slice($values, 0, 10, true);
    }

    public function getModerators($subreddit) {
        $url = sprintf("%s/r/%s/about/moderators.json",
            ENDPOINT_OAUTH,
            $subreddit
        );

        return Request::runCurl($url, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getUserComments($subreddit, $user){
        $url = sprintf("%s/comment/?subreddit=%s&author=%s&size=500",
            PUSHSHIFT_API,
            $subreddit,
            $user
        );

        return Request::runCurl($url);
    }

    public function getCommonWords($posts, $comments){
        $commonWords = array('a','I','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');

        $data = array();
        foreach($posts as $post){
            $text = preg_replace("/[^a-zA-Z0-9 ]+/", "", $post->full_text);
            $text = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$text);
            $words = explode(" ", $text);
            $text_title = preg_replace("/[^a-zA-Z0-9 ]+/", "", $post->title);
            $words = array_merge($words, explode(" ", $text_title));
            foreach($words as $word) {
                if ($word == ""){
                    continue;
                }
                $word = strtolower($word);
                if (array_key_exists(strtolower($word), $data)) {
                    $data[$word] += 1;
                } else {
                    $data[$word] = 0;
                }
            }
        }

        foreach($comments as $comment){
            $text = preg_replace("/[^a-zA-Z0-9 ]+/", "", $comment->body);
            $text = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$text);
            $words = explode(" ", $text);
            foreach($words as $word) {
                if ($word == ""){
                    continue;
                }
                $word = strtolower($word);
                if (array_key_exists($word, $data)) {
                    $data[$word] += 1;
                } else {
                    $data[$word] = 1;
                }
            }
        }
        arsort($data);
        return array_slice($data, 0,5);
    }

    public function getUserPosts($subreddit, $user){
        $url = sprintf("%s/submission/?subreddit=%s&author=%s&size=500",
            PUSHSHIFT_API,
            $subreddit,
            $user
        );

        return Request::runCurl($url);
    }

    public function getNumberOfCommentsAndDays($subreddit){
        $numberOfComments = array();
        $days = array();

        $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=month",
            ENDPOINT_OAUTH,
            $subreddit,
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);

        while(!empty($data)){
            $after = $data->data->after;
            foreach($data->data->children as $child){
                array_push($numberOfComments, $child->data->num_comments);
                $epoch = $child->data->created_utc;
                $dt = new DateTime("@$epoch");
                array_push($days, $dt->format('Y-m-d'));
            }
            if($after == null)
                break;

            $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=month&after=%s",
                ENDPOINT_OAUTH,
                $subreddit,
                $after
            );

            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        }

        $info = [
            "x" => $days,
            "y" => $numberOfComments
        ];

        return $info;
    }

    public function getPostPerDayInAMonth($subreddit){
        $numberOfPosts = array();
        $days = array();

        $urlSubRel = sprintf("%s/r/%s/new.json?limit=100",
            ENDPOINT_OAUTH,
            $subreddit
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        $timeOneMonthAgo = time() - 2678400; //2678400 one month in unix epoch time between two months, current date and one month ago
        while(!empty($data)){
            $after = $data->data->after;
            $ok = 0;
            foreach($data->data->children as $child){
                if($child->data->created_utc < $timeOneMonthAgo){
                    $ok = 1;
                    break;
                }
                $epoch = $child->data->created_utc;
                $dt = new DateTime("@$epoch");
                if(in_array($dt->format('Y-m-d'), $days)){
                    $index = array_search($dt->format('Y-m-d'), $days);
                    $numberOfPosts[$index]+=1;
                }
                else{
                    array_push($days, $dt->format('Y-m-d'));
                    array_push($numberOfPosts, 1);
                }
            }
            if($after == null || $ok == 1)
                break;

            $urlSubRel = sprintf("%s/r/%s/new.json?limit=100&after=%s",
                ENDPOINT_OAUTH,
                $subreddit,
                $after
            );

            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        }

        $info = [
            "x" => $days,
            "y" => $numberOfPosts
        ];

        return $info;
    }

}