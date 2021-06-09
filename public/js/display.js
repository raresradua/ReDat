        // /monitor and /monitor/ for heroku
        if (window.location.pathname == "/ReDat/monitor" || window.location.pathname == "/monitor/") {
            document.querySelector('main').style = " display :none;";
        } else {
            document.querySelector('main').style = "display : '';";
        }