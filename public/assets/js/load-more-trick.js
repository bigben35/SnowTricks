
$(document).ready(function() {
    let $tricks = $('.trick-container');
    let count = 8;
    let total = $tricks.length;
  console.log($tricks);
    // On cache tous les éléments au-delà de l'index 7
    $tricks.slice(count).hide();
  
    // Lorsque l'utilisateur clique sur le bouton "Load more"
    $('.load-more').click(function(e) {
      e.preventDefault();
  
      // On affiche les 4 éléments suivants
      $tricks.slice(count, count + 4).slideDown();
      count += 4;
  
      // Si tous les éléments ont été affichés, on cache le bouton "Load more"
      if (count >= total) {
        $('.load-more').addClass('hide-btn');
        
      }
    });
  
  });


  function countVisibleTricks() {
    
    let tricks = document.getElementsByClassName("trick-container");
    let count = 0;
    for (let i = 0; i < tricks.length; i++) {
        if (tricks[i].offsetParent !== null) {
            count++;
        }
    }
    return count;
}

// ----------------BOUTON FLECHE POUR REMONTER EN HAUT DE LA PAGE -----------------

const btnArrow = document.querySelector('.btn-arrow');
window.onscroll = function() {
  
  if (countVisibleTricks() >= 10) {
    btnArrow.classList.add('visible');
  } else {
    btnArrow.classList.remove('visible');
  }
};

btnArrow.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        left:0,
        behavior: 'smooth'
    })
});
