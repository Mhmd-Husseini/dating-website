const form = document.getElementById('registration-form');
form.addEventListener('submit', handleFormSubmit);

function handleFormSubmit(event) {
  event.preventDefault();

  const data = new FormData(event.target);

  axios.post('http://127.0.0.1:8000/api/register', data)
    .then(response => {
      const { user, authorisation } = response.data;
      localStorage.setItem('token', authorisation.token);
      alert(`User ${user.username} has been registered successfully!`);
      window.location.href = 'index.html';
    })
    .catch(error => {
      alert(error.response.data.message);
    });
}
