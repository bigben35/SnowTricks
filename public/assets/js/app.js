console.log("Bonjourrr");

const addVideo = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement('div');
    item.classList.add('form-group'); // ajoute la classe form-control à l'élément div parent

    const videoCount = collectionHolder.querySelectorAll('.form-group').length + 1;
    const label = document.createElement('label');
    label.innerHTML = 'Vidéo ' + videoCount; // ajoute le texte 'Video' à l'élément label
    label.setAttribute('for', collectionHolder.dataset.name + '_' + collectionHolder.dataset.index + '_mediaLink');


    const input = document.createElement('input');
    input.type = 'text';
    input.name = collectionHolder.dataset.name + '[' + collectionHolder.dataset.index + '][mediaLink]';
    input.id = label.getAttribute('for');
    input.classList.add('form-control'); // ajoute la classe form-control à l'élément input

  item.appendChild(label); // ajoute l'élément label à l'élément parent item
  item.appendChild(input); // ajoute l'élément input à l'élément parent item

  item.innerHTML = collectionHolder
    .dataset
    .prototype
    .replace(
      /__name__/g,
      collectionHolder.dataset.index
    );

item.querySelector('.btn-remove').addEventListener('click', () => item.remove());

  collectionHolder.appendChild(item);

  collectionHolder.dataset.index++;
};



document.querySelectorAll('.btn-new').forEach(btn => {
    btn.addEventListener("click", addVideo)});
    
    //problème avec forEach ??
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', (e) => e.currentTarget.closest('.form-control').remove())});



