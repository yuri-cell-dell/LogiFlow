document.addEventListener('click', function (event) {
  const add = event.target.closest('[data-add-product]');
  if (add) {
    const container = document.querySelector('#product-list');
    const template = document.querySelector('#product-template');
    if (container && template) {
      const clone = template.content.cloneNode(true);
      container.appendChild(clone);
    }
  }
  const remove = event.target.closest('[data-remove-product]');
  if (remove) remove.closest('.product-row')?.remove();
});
