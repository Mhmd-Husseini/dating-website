const form = document.getElementById('login-form');

form.addEventListener('submit', function(event) {
  event.preventDefault();
  
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  
  axios.post('http://127.0.0.1:8000/api/register', {
    email: email,
    password: password
  })
  .then(function(response) {
    const token = response.data.authorisation.token;
    localStorage.setItem('token', token);
    window.location.href = 'index.html';
  })
  .catch(function(error) {
    console.log(error);
    alert('Invalid email or password.');
  });
});
