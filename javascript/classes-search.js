const searchInput = document.querySelector('#searchQuery');

const resultsContainer =
  document.querySelector('.grid-container');

searchInput.addEventListener('input', async () => {

  const query = searchInput.value;

  const response = await fetch(
    `../ajax/search_classes.php?q=${query}`
  );

  const html = await response.text();

  resultsContainer.innerHTML = html;
});