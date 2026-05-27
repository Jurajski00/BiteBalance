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
    });
}

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
    });
}

const buttonLogoutUser = document.querySelector('#buttonLogoutUser');
if (buttonLogoutUser) {
    buttonLogoutUser.addEventListener('click', async (e) => {
        e.preventDefault();

        if (!confirm('Are you sure you want to securely log out of your session tracker instance?')) return;

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
    });
}

const formUpdateProfile = document.getElementById('formUpdateProfile');
const profileAlert = document.getElementById('profileAlert');

if (formUpdateProfile) {
    formUpdateProfile.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('inputProfileName').value;
        const surname = document.getElementById('inputProfileSurname').value;
        const username = document.getElementById('inputProfileUsername').value;
        const email = document.getElementById('inputProfileEmail').value;
        const weight = document.getElementById('inputProfileWeight').value;
        const height = document.getElementById('inputProfileHeight').value;
        const age = document.getElementById('inputProfileAge').value;

        const fd = new FormData();
        fd.append('action', 'updateProfile');
        fd.append('name', name);
        fd.append('surname', surname);
        fd.append('username', username);
        fd.append('email', email);
        fd.append('weight', weight);
        fd.append('height', height);
        fd.append('age', age);

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: fd });
            const result = await response.json();

            if (profileAlert) {
                profileAlert.className = `alert alert-${result.success ? 'success' : 'danger'} small`;
                profileAlert.textContent = result.message;
                profileAlert.classList.remove('d-none');
            }
        } catch (err) {
            console.error(err);
            if (profileAlert) {
                profileAlert.className = 'alert alert-danger small';
                profileAlert.textContent = 'A profile synchronization exception occurred.';
                profileAlert.classList.remove('d-none');
            }
        }
    });
}

const formChangePassword = document.getElementById('formChangePassword');
const passwordAlert = document.getElementById('passwordAlert');

if (formChangePassword) {
    formChangePassword.addEventListener('submit', async (e) => {
        e.preventDefault();

        const current = document.getElementById('inputCurrentPassword').value;
        const newPass = document.getElementById('inputNewPassword').value;
        const confirmPass = document.getElementById('inputConfirmPassword').value;

        if (newPass !== confirmPass) {
            if (passwordAlert) {
                passwordAlert.className = 'alert alert-danger small';
                passwordAlert.textContent = 'New password references do not match.';
                passwordAlert.classList.remove('d-none');
            }
            return;
        }

        const fd = new FormData();
        fd.append('action', 'changePassword');
        fd.append('current', current);
        fd.append('new', newPass);
        fd.append('confirm', confirmPass);

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: fd });
            const result = await response.json();

            if (passwordAlert) {
                passwordAlert.className = `alert alert-${result.success ? 'success' : 'danger'} small`;
                passwordAlert.textContent = result.message;
                passwordAlert.classList.remove('d-none');
            }

            if (result.success) {
                formChangePassword.reset();
            }
        } catch (err) {
            console.error(err);
            if (passwordAlert) {
                passwordAlert.className = 'alert alert-danger small';
                passwordAlert.textContent = 'A network execution or response payload error occurred.';
                passwordAlert.classList.remove('d-none');
            }
        }
    });
}

document.querySelectorAll('.btn-edit-user').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const row = document.getElementById(`user-row-${id}`);

        document.getElementById('editUserId').value = id;
        document.getElementById('editUserName').value = row.querySelector('.txt-name').textContent;
        document.getElementById('editUserSurname').value = row.querySelector('.txt-surname').textContent;
        document.getElementById('editUserUsername').value = row.querySelector('.txt-username').textContent;
        document.getElementById('editUserEmail').value = row.querySelector('.txt-email').textContent;
        document.getElementById('editUserWeight').value = row.querySelector('.txt-weight').textContent;
        document.getElementById('editUserHeight').value = row.querySelector('.txt-height').textContent;
        document.getElementById('editUserAge').value = row.querySelector('.txt-age').textContent;

        const roleBadge = row.querySelector('.badge-role');
        document.getElementById('editUserIsAdmin').checked = roleBadge.textContent.trim() === 'Admin';

        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modal.show();
    });
});

const formAdminEditUser = document.getElementById('formAdminEditUser');
const adminAlert = document.getElementById('adminAlert');

if (formAdminEditUser) {
    formAdminEditUser.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('editUserId').value;
        const name = document.getElementById('editUserName').value;
        const surname = document.getElementById('editUserSurname').value;
        const username = document.getElementById('editUserUsername').value;
        const email = document.getElementById('editUserEmail').value;
        const weight = document.getElementById('editUserWeight').value;
        const height = document.getElementById('editUserHeight').value;
        const age = document.getElementById('editUserAge').value;
        const isAdmin = document.getElementById('editUserIsAdmin').checked ? '1' : '0';

        const fd = new FormData();
        fd.append('action', 'adminUpdateUser');
        fd.append('id', id);
        fd.append('name', name);
        fd.append('surname', surname);
        fd.append('username', username);
        fd.append('email', email);
        fd.append('weight', weight);
        fd.append('height', height);
        fd.append('age', age);
        fd.append('is_admin', isAdmin);

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: fd });
            const result = await response.json();

            if (adminAlert) {
                adminAlert.className = `alert alert-${result.success ? 'success' : 'danger'} small`;
                adminAlert.textContent = result.message;
                adminAlert.classList.remove('d-none');
            }

            if (result.success) {
                const row = document.getElementById(`user-row-${id}`);
                row.querySelector('.txt-name').textContent = name;
                row.querySelector('.txt-surname').textContent = surname;
                row.querySelector('.txt-username').textContent = username;
                row.querySelector('.txt-email').textContent = email;
                row.querySelector('.txt-weight').textContent = weight;
                row.querySelector('.txt-height').textContent = height;
                row.querySelector('.txt-age').textContent = age;

                const roleBadge = row.querySelector('.badge-role');
                if (isAdmin === '1') {
                    roleBadge.className = 'badge bg-danger badge-role';
                    roleBadge.textContent = 'Admin';
                } else {
                    roleBadge.className = 'badge bg-info text-dark badge-role';
                    roleBadge.textContent = 'User';
                }

                bootstrap.Modal.getInstance(document.getElementById('modalEditUser')).hide();
            }
        } catch (err) {
            console.error(err);
        }
    });
}

document.querySelectorAll('.btn-delete-user').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.getAttribute('data-id');
        if (!confirm('Are you absolute certain you wish to isolate and delete this record?')) return;

        const fd = new FormData();
        fd.append('action', 'adminDeleteUser');
        fd.append('id', id);

        try {
            const response = await fetch('userApi.php', { method: 'POST', body: fd });
            const result = await response.json();

            if (adminAlert) {
                adminAlert.className = `alert alert-${result.success ? 'success' : 'danger'} small`;
                adminAlert.textContent = result.message;
                adminAlert.classList.remove('d-none');
            }

            if (result.success) {
                document.getElementById(`user-row-${id}`).remove();
            }
        } catch (err) {
            console.error(err);
        }
    });
});