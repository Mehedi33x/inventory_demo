<template>
    <div class="d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px;">
            <h2 class="mb-4 text-center">Login</h2>
            <form @submit.prevent="handleLogin" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" v-model="data.email" required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" v-model="data.password" required />
                </div>
                <p>Dont have account? <router-link :to="{ name: 'auth.register' }">Register </router-link></p>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            data: {
                email: '',
                password: '',
            }
        }
    },
    methods: {
        handleLogin() {
            try {
                const response = axios.post('/api/login', this.data);

                // Example: assume the API returns { token: 'abc123', user: {...} }
                const token = response.data.token;

                // Store token in localStorage
                localStorage.setItem('auth_token', token);

                // Optionally store user data
                localStorage.setItem('user', JSON.stringify(response.data.user));

                // Redirect to dashboard or any authenticated route
                this.$router.push({ name: 'home' }); // Make sure 'Dashboard' route exists

            } catch (error) {
                console.error('Login failed:', error);
                alert('Invalid credentials or server error.');
            }
        }
    }

}
</script>
