<template>
    <div class="container">
        <h3 class="text-center mt-4">Sale List</h3>
        <button @click="createSale" class="btn btn-success text-end">Create</button>

        <div class="container mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>VAT (%)</th>
                        <th>Total Price</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody v-if="sales.length > 0">
                    <tr v-for="(item, index) in sales" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>
                            <img v-if="item.product?.image_url" :src="item.product.image_url" alt="Product Image"
                                style="height: 40px; width: 40px;" />
                        </td>
                        <td>{{ item.product?.name || 'N/A' }}</td>
                        <td>{{ item.quantity }}</td>
                        <td>{{ item.price }}</td>
                        <td>{{ item.discount ?? 0 }}</td>
                        <td>{{ item.vat ?? 0 }}</td>
                        <td>{{ item.total_price }}</td>
                        <td>{{ item.paid }}</td>
                        <td>{{ item.due }}</td>
                        <td>
                            <button @click="editSale(item.id)" class="btn btn-primary btn-sm mx-1">Edit</button>
                            <button :disabled="loading" @click="deleteSale(item.id)" class="btn btn-danger btn-sm mx-0">
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
            <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="fetchSales" />
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import axios from "axios";

export default {
    data() {
        return {
            sales: [],
            currentPage: 1,
            perPage: 5,
            totalPages: 0,
            loading: false,
        };
    },
    methods: {
        createSale() {
            this.$router.push({ name: "sale.create" });
        },
        editSale(id) {
            this.$router.push({ name: "sale.edit", params: { id } });
        },
        async deleteSale(id) {
            const confirm = await Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
            });

            if (confirm.isConfirmed) {
                try {
                    await axios.delete(`/api/sale/${id}`);
                    this.sales = this.sales.filter((s) => s.id !== id);
                    Swal.fire("Deleted!", "Sale has been deleted.", "success");
                } catch (err) {
                    console.error(err);
                    Swal.fire("Error", "Something went wrong.", "error");
                }
            }
        },
        async fetchSales(page = 1) {
            try {
                const response = await axios.get('/api/sale', {
                    params: { page, perPage: this.perPage },
                });

                this.sales = response.data.data;
                this.currentPage = response.data.currentPage;
                this.totalPages = response.data.totalPages;
            } catch (error) {
                console.error("Error fetching sales:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch sale list.",
                });
            }
        }

    },
    created() {
        this.fetchSales();
    },
};
</script>

<style scoped>
.no-data {
    text-align: center;
}
</style>
