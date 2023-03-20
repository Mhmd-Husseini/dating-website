const searchButton = document.getElementById('search-button');

searchButton.addEventListener('click', () => {
  const token = localStorage.getItem('token');

  const headers = {
    'Authorization': `Bearer ${token}`
  };

  const params = {
    min_age: document.getElementById('min-age').value,
    max_age: document.getElementById('max-age').value,
    location: document.getElementById('location_id').value,
    search: document.getElementById('search').value
  };

  axios.get('http://127.0.0.1:8000/api/users', {
    headers: headers,
    params: params
  })
  .then(response => {
    const resultsContainer = document.getElementById('results-container');
    resultsContainer.innerHTML = '';

    response.data.users.forEach(user => {
      const userElement = document.createElement('div');
      userElement.innerHTML = `<p>ID: ${user.user_id}</p><p>Name: ${user.username}</p><p>Age: ${user.age}</p><p>Location: ${user.location}</p>`;
      resultsContainer.appendChild(userElement);
    });
  })
  .catch(error => {
    console.error(error);
  });
});

