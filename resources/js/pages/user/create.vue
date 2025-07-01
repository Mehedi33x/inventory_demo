<template>
    <div class="container">
        <h3 class="text-center mt-4">Create User</h3>
        <hr>
        <div class="mx-5 mt-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input v-model="form.name" type="text" name="name" class="form-control"
                            placeholder="Enter Name">
                        <span v-if="errors.name" class="text-danger"><small>{{ errors.name }}</small></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="tel" name="phone" class="form-control"
                            placeholder="Enter Phone Number">
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" name="email" class="form-control"
                            placeholder="Enter Email">
                        <span v-if="errors.email" class="text-danger"><small>{{ errors.email }}</small></span>

                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input v-model="form.password" type="password" name="password" class="form-control"
                            placeholder="Enter password">
                        <span v-if="errors.password" class="text-danger"><small>{{ errors.password }}</small></span>

                    </div>

                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Address</label>
                    <textarea v-model="form.address" class="form-control" id="exampleFormControlTextarea1"
                        rows="3"></textarea>
                </div>
            </div>
            <button @click="saveData" type="button" class="btn btn-success">Success</button>

        </div>
    </div>
</template>

<script>
import axios from "axios";
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            form: {
                name: '',
                phone: '',
                email: '',
                password: '',
                address: ''
            },
            errors: {}
        }
    },
    methods: {
        async saveData() {
            this.errors = {}
            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('phone', this.form.phone);
            formData.append('email', this.form.email);
            formData.append('password', this.form.password);
            formData.append('address', this.form.address);
            try {
                const response = await axios.post('/api/user/create', formData);
                // console.log(response);
                if (response.status === 201) {
                    this.$router.push({
                        name:'user.index'
                    });
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Success',
                        text: 'User has created successfully',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: true,
                    });

                }

            } catch (error) {
                const serverErrors = error.response.data.errors;
                this.errors = Object.fromEntries(
                    Object.entries(serverErrors).map(([field, messages]) => [field, messages[0]])
                );
            }
        }
    }

}
</script>