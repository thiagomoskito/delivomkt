<!-- footer begin -->
<footer class="text-light section-dark">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-md-12">
        <div class="d-lg-flex align-items-center justify-content-between text-center">
          <div>
            <img src="images/logo.png" class="w-150px" alt=""><br>
            <div class="social-icons mb-sm-30 mt-4">
              <!-- <a href="#"><i class="fa-brands fa-facebook-f"></i></a> -->
              <a href="https://www.instagram.com/delivooficial/"><i
                  class="fa-brands fa-instagram"></i></a>
              <a href="https://www.x.com/delivooficial/"><i class="fa-brands fa-x-twitter"></i></a>
              <a href="https://www.youtube.com/delivooficial/"><i class="fa-brands fa-youtube"></i></a>
            </div>

          </div>

          <div>
            <h3 class="fs-20">Contato</h3>
            +55 61 9 9999-8877<br>
            <a href="mailto:contato@delivomkt.com.br">contato@delivomkt.com.br</a>
          </div>

          <div>
            <h3 class="fs-20">Parceiros</h3>
            <a href="https://hairbrasilia.com.br/" target="_blank"><img
                src="images/LOGO-HAIR-BRASILAI-PR-BRASILIA1-224x64.png" class="w-150px"
                alt="Hair Brasilia"></a><br><br>
            <a href="https://truvo.com.br/" target="_blank"><img src="images/truvo-logo.svg" class="w-150px"
                alt="Truvô"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="subfooter">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          Copyright 2025 - Delivo, de longe, o mais próximo de você!
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- footer end -->

<!-- Javascript Files
    ================================================== -->
<script src="js/vendors.js"></script>
<script src="js/designesia.js"></script>
<script src="js/countdown-custom.js"></script>
<script src="js/custom-marquee.js"></script>

<script>
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visivel');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.5
  });

  document.querySelectorAll('strong').forEach(element => {
    observer.observe(element);
  });
</script>