<template>
    <div class="container">
        <h3 class="text-center mt-4">Stock List</h3>
        <button @click="createStock" class="btn btn-success text-end">Create</button>
        <div class="container mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody v-if="stocks.length > 0">
                    <tr v-for="(item, index) in stocks" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td><img v-if="item.product?.image_url" :src="item.product?.image_url" alt="Product Image"
                                style="height: 40px;width: 40px;" /></td>
                        <td>{{ item.product?.name || 'N/A' }}</td>
                        <td>{{ item.quantity }}</td>
                        <td>
                            <button @click="editStock(item.id)" class="btn btn-primary btn-sm mx-1">Edit</button>
                            <button :disabled="loading" @click="deleteStock(item.id)"
                                class="btn btn-danger btn-sm mx-0">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="4" class="no-data">No data found</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="fetchStocks" />
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import axios from "axios";

export default {
    data() {
        return {
            stocks: [],
            currentPage: 1,
            perPage: 5,
            totalPages: 0,
            loading: false,
        };
    },
    methods: {
        createStock() {
            this.$router.push({ name: "stock.create" });
        },
        editStock(id) {
            this.$router.push({ name: "stock.edit", params: { id } });
        },
        async deleteStock(id) {
            const confirm = await Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
            });

            if (confirm.isConfirmed) {
                try {
                    await axios.delete(`/api/stock/${id}`);
                    this.stocks = this.stocks.filter((s) => s.id !== id);
                    Swal.fire("Deleted!", "Stock has been deleted.", "success");
                } catch (err) {
                    console.error(err);
                    Swal.fire("Error", "Something went wrong.", "error");
                }
            }
        },
        async fetchStocks(page = 1) {
            try {
                const response = await axios.get('/api/stock', {
                    params: { page, perPage: this.perPage },
                });

                this.stocks = response.data.data;
                this.currentPage = response.data.currentPage;
                this.totalPages = response.data.totalPages;
            } catch (error) {
                console.error("Error fetching stocks:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch stock list.",
                });
            }
        }
    },
    created() {
        this.fetchStocks();
    },
};
</script>

<style scoped>
.no-data {
    text-align: center;
}
</style>
