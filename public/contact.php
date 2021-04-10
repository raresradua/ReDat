<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReDat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>
<section>
    <div class="contactForm">
        <form action="contact">
            <div id="titleMessage">
            <span>SEND US A MESSAGE</span>
            </div>
            <div id="inputText">
            <input type="text" name="fullname" placeholder="Full Name">
                            
            </div>
            <div id="inputText">
            <input type="text" name="email" placeholder="E-mail">
                            
            </div>
            <div id="inputMessage">
            <textarea name="message" id="message" placeholder="Your Message"></textarea>
                            
            </div>
            <button class="send-button" type="submit"> Send </button>
        </form>
    </div>
    
</section>

<?php
    include ("../templates/footer.php");
?>
</body>
</html>