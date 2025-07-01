<template>
    <div class="container">
        <h3 class="text-center mt-4">User List</h3>
        <router-link :to="{ name: 'user.create' }" class="btn btn-success text-end">Create</router-link>
        <div class="container mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody v-if="users.length > 0">
                    <tr v-for=" (item, index) in users" :key="index">
                        <th scope="row">{{ (currentPage - 1) * perPage + index + 1 }}</th>
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.phone }}</td>
                        <td>{{ item.address }}</td>
                        <td>
                            <button class="btn btn-success btn-sm">View</button>
                            <button class="btn btn-primary btn-sm mx-1">Edit</button>
                            <button class="btn btn-danger btn-sm mx-0">Delete</button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td :colspan="6" class="no-data">No data found</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="fetchUsers" />
        </div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserList',
    data() {
        return {
            users: [],
            currentPage: 1,
            perPage: 2,
            totalPages: 0,

        };
    },

    methods: {
        async fetchUsers(page = 1) {
            try {
                const response = await axios.get(`/api/user`, {
                    params: { page, perPage: this.perPage }
                });
                this.users = response.data.data;
                this.currentPage = response.data.currentPage;
                this.totalPages = response.data.totalPages;
            } catch (error) {
                console.error("Failed to fetch users:", error.message);
            }
        },
    },
    created() {
        this.fetchUsers();
    },

};
</script>

<style scoped>
.no-data {
    text-align: center;
    vertical-align: middle;
    height: 20px;
}

</style>