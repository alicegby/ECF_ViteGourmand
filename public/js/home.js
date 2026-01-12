// HOME.JS – Galerie d'images

document.addEventListener('DOMContentLoaded', () => {

  const slides = document.querySelectorAll('.slide');
  console.log('Slides trouvées:', slides.length);

  if (slides.length === 0) return; // si pas de slides, on quitte

  let index = 0;

  // Fonction pour afficher une slide et cacher les autres
  function showSlide(i) {
    slides.forEach(slide => slide.style.opacity = '0'); // on cache tout
    slides[i].style.opacity = '1';                     // on affiche celle-ci
  }

  // Affiche la première image au chargement
  showSlide(index);

  // Change de slide toutes les 3 secondes
  setInterval(() => {
    index = (index + 1) % slides.length;
    showSlide(index);
  }, 3000);

});