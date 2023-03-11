const addVideo = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

  const item = document.createElement('div');
 const input = document.querySelector('input');
 input.classList.add('form-control');  // ne marche pas

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

// const addVideo = (e) => {
//     const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
  
//     const item = document.createElement('div');
  
//     item.innerHTML = collectionHolder
//       .dataset
//       .prototype
//       .replace(
//         /__name__/g,
//         collectionHolder.dataset.index
//       );
  
//     collectionHolder.appendChild(item);
  
//     collectionHolder.dataset.index++;
//   };

//   document
//   .querySelectorAll('.btn-new')
//   .forEach(btn => {
//       btn.addEventListener("click", addVideo)
//   });

