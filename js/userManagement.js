// ---USER REGISTR---

const formRegister = document.querySelector('#formRegister');
if (formRegister) {
    formRegister.addEventListener('submit', async (e) => {
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

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: register });
            const data = await response.json();

            if (data.success === true) {
                window.location.href = 'index.php';
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Critical error:', error);
        }

        // fetch('userApi.php', {
        //     method: 'POST',
        //     body: register
        // })
        // .then(response => {
        //     return response.json();
        // })
        // .then(data => {
        //     if (data.success === true) {
        //         window.location.href = 'index.php';
        //         // alert(data.message);
        //     } else {
        //         alert(data.message);
        //     }
        // })
        // .catch(error => {
        //     console.error("Critical error:", error);
        // });
    });
}

// ---USER LOGIN---

const formLogin = document.querySelector('#formLogin');
if (formLogin) {
        formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.querySelector('#username').value;
        const password = document.querySelector('#password').value;

        const login = new FormData();
        login.append('action', 'loginUser');
        login.append('loginUsername', username);
        login.append('loginPassword', password);

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: login });
            const data = await response.json();

            if (data.success === true) {
                window.location.href = 'index.php';
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Critical error:', error);
        }

        // fetch('userApi.php', {
        //     method: 'POST',
        //     body: login
        // })
        // .then(response => {
        //     return response.json();
        // })
        // .then(data => {
        //     if (data.success === true) {
        //         // alert(data.message);
        //         window.location.href = 'index.php';
        //     } else {
        //         alert(data.message);
        //     }
        // })
        // .catch(error => {
        //     console.error("Critical error:", error)
        // });
    });
}

// ---USER LOGOUT---

const buttonLogoutUser = document.querySelector('#buttonLogoutUser');
if (buttonLogoutUser) {
    buttonLogoutUser.addEventListener('click', async (e) => {
        e.preventDefault();

        const logout = new FormData();
        logout.append('action', 'logoutUser');

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: logout });
            const data = await response.json();

            if (data.success === true) {
                window.location.href = 'welcome.php';
            } else {
                alert('Something went wrong with logout');
            }
        } catch (error) {
            console.error('Critical error:', error);
        }

        // fetch('userApi.php', {
        //     method: 'POST',
        //     body: logout
        // })
        // .then(response => {
        //     return response.json();
        // })
        // .then(data => {
        //     if (data.success === true) {
        //         alert(data.message);
        //         window.location.href = 'welcome.php';
        //     } else {
        //         alert('Something went wrong with logout');
        //     }
        // })
        // .catch(error => {
        //     console.error("Critical error:", error);
        // });
    });
}