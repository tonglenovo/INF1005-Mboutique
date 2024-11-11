<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>

        </style>

    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <main class="container">
            <header class="jumbotron text-center" id="home">
                <h1 class="display-4">About Us</h1>

            </header>

            <div class="slideshow-container">

                <div class="mySlides" style="text-align:center">
                    <div class="numbertext">1 / 3</div>
                    <img src="images/AboutUs_Men2.jpg" alt="AboutUs_Men2" style="width:50%">
                </div>

                <div class="mySlides" style="text-align:center">
                    <div class="numbertext">2 / 3</div>
                    <img src="images/AboutUs_Men3.png" alt="AboutUs_Men3" style="width:50%">
                </div>

                <div class="mySlides" style="text-align:center">
                    <div class="numbertext">3 / 3</div>
                    <img src="images/AboutUs_Men4.jpg" alt="AboutUs_Men4" style="width:50%">
                </div>

            </div>
            <br>

            <div style="text-align:center">
                <span class="dot"></span> 
                <span class="dot"></span> 
                <span class="dot"></span> 
            </div>


            <div class="container">
                <div class="row">
                    <div style="text-align:justify" class="col-sm-12"><br>Welcome to M Boutique, where we strive to provide you with the latest trends, and fashion inspiration to help you look your best.<br><br>
                        Our team of fashion experts is dedicated to curating a collection of high-quality clothing and accessories that embody sophistication, style, and elegance. Whether you are looking for casual wear, formal wear, or something in between, we have got you covered.<br><br>
                        We believe that fashion should be accessible to everyone, regardless of their budget or personal style. That is why we offer a wide range of products at affordable prices, without compromising on quality.<br><br>
                        Thank you for choosing M Boutique as your go-to destination for all things fashion. We hope to inspire you to explore new styles and express yourself through fashion.<br><br>
                    </div>
                </div>

            </div>
        </main>
        <?php include'footer.inc.php' ?>
        <script>
            let slideIndex = 0;
            showSlides();
            function showSlides() {
                let i;
                let slides = document.getElementsByClassName("mySlides");
                let dots = document.getElementsByClassName("dot");
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                slideIndex++;
                if (slideIndex > slides.length) {
                    slideIndex = 1;
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                slides[slideIndex - 1].style.display = "block";
                dots[slideIndex - 1].className += " active";
                setTimeout(showSlides, 2000); // Change image every 2 seconds
            }
        </script>
    </body>
</html>
