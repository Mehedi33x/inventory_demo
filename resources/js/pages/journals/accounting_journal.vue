<template>
    <div class="container my-4">
        <h3 class="text-center">{{ page_title }}</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in journals" :key="item.id">
                    <td>{{ item?.sale?.invoice_number }}</td>
                    <td>{{ item.account }}</td>
                    <td>
                        <span :class="item.type === 'debit' ? 'text-success' : 'text-danger'">
                            {{ item.type }}
                        </span>
                    </td>
                    <td>{{ parseFloat(item.amount).toFixed(2) }}</td>
                    <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                </tr>
            </tbody>
            <tfoot v-if="journals.length === 0">
                <tr>
                    <td colspan="5" class="text-center text-muted">No data found</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            page_title: "Accounting Journals",
            journals: [],
        };
    },
    mounted() {
        this.fetchJournals();
    },
    methods: {
        async fetchJournals() {
            try {
                const response = await this.$axios.get("/api/journals");
                this.journals = response.data;
            } catch (e) {
                console.error("Failed to fetch journal entries", e);
            }
        },
    },
};
</script>
<style scoped>
.table th,
.table td {
    vertical-align: middle;
}
</style>
