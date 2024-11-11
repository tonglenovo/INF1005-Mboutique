<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>
        <main class="container">
            <div>
                <img class="homeSlides" src="images/home1.png" alt='home1' style="width:100%">
                <img class="homeSlides" src="images/home2.png" alt='home2' style="width:100%">
                <img class="homeSlides" src="images/home3.png" alt='home3' style="width:100%">
            </div>



            <section class="homeText" style="max-width:600px; text-align: center; display: inline">
                <h2 class="wide">All About Men's Fashion</h2>

                <p class="justify">Welcome to M Boutique, where style and sophistication meet. We understand that fashion is not just about looking good, but also feeling confident and confortable in your own skin. We offer a wide range of options for every occasion, whether it's a business meeting, a night out with friends, or a casual weekend at home.<br><br>

                </p>
            </section>
            <div class="container">
                <div class="row text-center">
                    <div class="col-sm-12">
                        <div class="row-padding center light-grey">
                            <div class="third">
                                <a href='viewClothing.php?type=T-shirt'>
                                    <img src="images/mentop.jpg" alt="mentop" style="width:40%">
                                </a>

                            </div>


                            <div class="third">
                                <a href='viewClothing.php?type=Outerwear'>
                                    <img src="images/mentop2.jpg" alt="mentop2" style="width:40%">
                                </a>

                            </div>

                            <div class="w3-third">
                                <a href='viewClothing.php?type=Short'>
                                    <img src="images/mensbottom.jpg" alt="mensbottom" style="width:40%">
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="Container mt-3 mb-3 text-center">
                <div class="Subscribe" style="background-color: #4099FF;">
                    <h3>Subscribe to our newsletter</h3>
                    <p>Get E-mail updates about our latest shop and <span>special offers.</span>
                    <form action="/process_newsletter.php" class="mb-3">
                        <input type="email" placeholder="Email Address" id="mail">
                        <button id="btn_subscribe">Subscribe</button>
                    </form>
                    <!--                <div class="Loading">
                                        <div class="LoadingDot"></div>
                                        <div class="LoadingDot"></div>
                                        <div class="LoadingDot"></div>
                                        <div class="LoadingDot"></div>
                                        <span>Subscribing</span>
                                    </div>
                                    <div class="Complete">
                                        <div class="Tick">
                                            <svg width="32px" height="25px" viewBox="0 0 32 25">
                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="Artboard" transform="translate(-384.000000, -217.000000)" fill-rule="nonzero" fill="#FFFFFF">
                                            <g id="Group" transform="translate(360.000000, 189.000000)">
                                            <path d="M27.4142136,40.5857864 C26.633165,39.8047379 25.366835,39.8047379 24.5857864,40.5857864 C23.8047379,41.366835 23.8047379,42.633165 24.5857864,43.4142136 L34,52.8284271 L55.4142136,31.4142136 C56.1952621,30.633165 56.1952621,29.366835 55.4142136,28.5857864 C54.633165,27.8047379 53.366835,27.8047379 52.5857864,28.5857864 L34,47.1715729 L27.4142136,40.5857864 Z" id="Path-2"></path>
                                            </g>
                                            </g>
                                            </g>
                                            </svg>
                                        </div>-->
                    <!--                    <div id="hide">
                                            <h3>Thank you for subscribing</h3>
                                            <span>You will receive a confirmation email shortly</span>
                                        </div>-->
                </div>
            </div>
        </main>

        <script>
            // Automatic Slideshow - change image every 3 seconds
            var myIndex = 0;
            carousel();

            function carousel() {
                var i;
                var x = document.getElementsByClassName("homeSlides");
                for (i = 0; i < x.length; i++) {
                    x[i].style.display = "none";
                }
                myIndex++;
                if (myIndex > x.length) {
                    myIndex = 1
                }
                x[myIndex - 1].style.display = "block";
                setTimeout(carousel, 3000);
            }
        </script>



        <?php include "footer.inc.php"; ?>
    </body>
</html>
