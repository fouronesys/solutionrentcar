 <!-- Footer Start -->
        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s" style="background-color: #333; color:white;">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="footer-item d-flex flex-column">
                            <div class="footer-item">
                                <h4 class="text-white mb-4">ABOUT</h4>
                                <p class="mb-3">Welcome to <?php echo $title;?> We are a car rental company committed to offering you the best mobility experience. We have worked to provide you with quality service, modern vehicles, and competitive prices so you can enjoy your trip or your daily life without worries.</p>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="text-white mb-4">Working hours</h4>
                            <div class="mb-3">
                               <h6 class="text-white mb-0">Monday - Sunday:</h6>
                               <p class="text-white mb-0">24 hours a day, 7 days a week</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="text-white mb-4">CONTACT</h4>
                            <li><i class="fa fa-map-marker-alt me-2"></i> <?php echo StockData::getFPrincipal($selstock)->address;?></li>
                            <li><i class="fas fa-envelope me-2"></i> <?php echo StockData::getFPrincipal($selstock)->email;?></li>
                            <li><i class="fab fa-whatsapp me-2"></i> <?php echo StockData::getFPrincipal($selstock)->phone;?></li>
                            <li class="mb-3"><i class="fas fa-phone me-2"></i> <?php echo StockData::getFPrincipal($selstock)->phone2;?></li>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        
        <!-- Copyright Start -->
        <div class="container-fluid copyright py-4" style="background-color: #222;">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-md-12 text-center text-md-start mb-md-0">
                        <span class="text-white"><a href="#" class="border-bottom text-white"></a>Todos los derechos reservados.</span>
                    </div>
                   
                </div>
            </div>
        </div>
        <!-- Copyright End -->


       
  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="gray"/></svg></div>


  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/jquery.timepicker.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
  </body>
</html>
