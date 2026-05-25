let slideIndex = 1;
showSlides(slideIndex);

// Function to control the previous/next buttons
function plusSlides(n) {
  showSlides(slideIndex += n);
}

function showSlides(n) {
  let slides = document.getElementsByClassName("slides");
  let carouselSlides = document.querySelector(".slides");
  
  // Handle going past the last slide (loops to the first)
  if (n > slides.length) { 
    slideIndex = 1;
  }
  
  // Handle going past the first slide (loops to the last)
  if (n < 1) { 
    slideIndex = slides.length;
  }

  // Move the slides container to the correct position
  const offset = -100 * (slideIndex - 1);
  carouselSlides.style.transform = `translateX(${offset}%)`;
}