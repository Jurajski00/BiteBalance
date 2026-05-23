// ---USER REGISTRATION---

document.querySelector('#formRegister').addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.querySelector('#name').value;
    const surname = document.querySelector('#surname').value;
    const email = document.querySelector('#email').value;
    const username = document.querySelector('#username').value;
    const password = document.querySelector('#password').value;
    const weight = document.querySelector('#weight').value;
    const height = document.querySelector('#height').value;
    const age = document.querySelector('#age').value;

    const register = new FormData();
    register.append('action', 'registerUser');
    register.append('registerName', name);
    register.append('registerSurname', surname);
    register.append('registerEmail', email);
    register.append('registerUsername', username);
    register.append('registerPassword', password);
    register.append('registerWeight', weight);
    register.append('registerHeight', height);
    register.append('registerAge', age);

    fetch('api.php', {
        method: 'POST',
        body: register
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        if (data.success === true) {
            document.querySelector('#formRegister').reset();
            // alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Critical error:", error);
    });
});