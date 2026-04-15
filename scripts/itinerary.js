const icon = document.getElementById('favIcon');
let isFavorited = false;

icon.addEventListener('click', () => {
  isFavorited = !isFavorited;
  icon.setAttribute('fill', isFavorited ? 'red' : '#434653');
});
