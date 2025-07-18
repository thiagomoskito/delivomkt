<!-- Lightbox-Code -->

<div class="lightbox" id="lightbox">
  <span class="close-btn">&times;</span>
  <img class="lightbox-img" id="lightbox-img">
  <div class="caption" id="caption"></div>
</div>

<style>
  .gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .gallery-img {
    /* width: 300px; */
    height: auto;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s;
  }

  .gallery-img:hover {
    transform: scale(1.02);
  }

  /* Lightbox */
  .lightbox {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    overflow: auto;
  }

  .lightbox-img {
    display: block;
    margin: 60px auto;
    max-width: 90%;
    max-height: 80%;
    animation: zoom 0.3s;
  }

  @keyframes zoom {
    from {
      transform: scale(0.8)
    }

    to {
      transform: scale(1)
    }
  }

  .caption {
    margin: 15px auto;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #fff;
    font-size: 1.2em;
    padding: 10px 0;
  }

  .close-btn {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
  }

  .close-btn:hover {
    color: #ccc;
  }

  @media (max-width: 768px) {
    .lightbox-img {
      max-width: 95%;
      max-height: 85%;
      margin: 40px auto;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todas as imagens da galeria
    const galleryImages = document.querySelectorAll('.gallery-img');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const caption = document.getElementById('caption');
    const closeBtn = document.querySelector('.close-btn');

    // Abre o lightbox quando uma imagem é clicada
    galleryImages.forEach(img => {
      img.addEventListener('click', function() {
        lightbox.style.display = 'block';
        lightboxImg.src = this.src;
        caption.textContent = this.alt;
      });
    });

    // Fecha o lightbox quando o botão de fechar é clicado
    closeBtn.addEventListener('click', function() {
      lightbox.style.display = 'none';
    });

    // Fecha o lightbox quando clica no overlay (fora da imagem)
    lightbox.addEventListener('click', function(e) {
      if (e.target === lightbox) {
        lightbox.style.display = 'none';
      }
    });

    // Fecha o lightbox com a tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && lightbox.style.display === 'block') {
        lightbox.style.display = 'none';
      }
    });
  });
</script>