<template>
    <div class="container mt-4">
        <h4 class="mb-3">Accounting Journal Entries</h4>
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
                <tr v-for="entry in journals" :key="entry.id">
                    <td>{{ entry.sale_id }}</td>
                    <td>{{ entry.account }}</td>
                    <td>
                        <span :class="entry.type === 'debit' ? 'text-success' : 'text-danger'">
                            {{ entry.type }}
                        </span>
                    </td>
                    <td>{{ parseFloat(entry.amount).toFixed(2) }}</td>
                    <td>{{ new Date(entry.created_at).toLocaleString() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
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

