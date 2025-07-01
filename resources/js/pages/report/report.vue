<template>
  <div class="container mt-4">
    <h3 class="text-center">{{ page_title }}</h3>
    <hr />
    <div class="text-end mb-1">
      <div class="border rounded p-3 bg-light mb-4">
        <div class="row align-items-end g-2">
          <div class="col-md-4">
            <label class="form-label text-start w-100">Search by Invoice Number</label>
            <input type="text" v-model="filters.search" class="form-control" placeholder="Type to search..." />
          </div>
          <div class="col-md-3">
            <label class="form-label text-start w-100">From Date</label>
            <input type="date" v-model="filters.from_date" class="form-control" />
          </div>
          <div class="col-md-3">
            <label class="form-label text-start w-100">To Date</label>
            <input type="date" v-model="filters.to_date" class="form-control" />
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-success w-100" @click="fetchSalesData">Search</button>
          </div>
        </div>
      </div>
      <button class="btn btn-outline-primary btn-sm me-2" @click="printReport">Print</button>
      <button class="btn btn-outline-success btn-sm me-2" @click="exportExcel">Excel</button>
      <button class="btn btn-outline-danger btn-sm" @click="exportPDF">PDF</button>
    </div>
    <div class="d-flex justify-content-between mb-3">
      <div>
        <p>Report Date: {{ report_date }}</p>
      </div>
    </div>
    <div class="table-responsive" id="printArea">
      <table class="table table-bordered text-center">
        <thead class="table-light">
          <tr>
            <th>SL</th>
            <th>Invoice Number</th>
            <th>Product Name</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Vat</th>
            <th>Discount</th>
            <th>Total Amount</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Sale Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in salesReportData" :key="item.id">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ item.invoice_number }}</td>
            <td class="text-center">{{ item?.product?.name }}</td>
            <td class="text-center">{{ item.price }}</td>
            <td class="text-center">{{ item.quantity }}</td>
            <td class="text-center">{{ item.vat }}</td>
            <td class="text-center">{{ item.discount }}</td>
            <td class="text-center">{{ item.total_price }}</td>
            <td class="text-center">{{ item.paid }}</td>
            <td class="text-center">{{ item.due }}</td>
            <td class="text-center">{{ $helpers.formatDate(item.created_at) }}</td>
          </tr>
        </tbody>
        <tfoot v-if="salesReportData.length === 0">
          <tr>
            <td colspan="10" class="text-center text-danger">No data found.</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "SalesReport",
  data() {
    return {
      page_title: "Sales Report",
      report_date: this.$moment().format("DD MMMM, YYYY"),
      salesReportData: [],
      filters: {
        search: "",
        from_date: "",
        to_date: "",
      },
    };
  },
  methods: {
    async fetchSalesData() {
      if (
        this.filters.from_date &&
        this.filters.to_date &&
        this.filters.to_date < this.filters.from_date
      ) {
        this.$Swal.fire("Error", "To Date cannot be before From Date", "error");
        return;
      }

      try {
        const response = await axios.get("/api/report", {
          params: {
            from_date: this.filters.from_date,
            to_date: this.filters.to_date,
            search: this.filters.search,
          },
        });
        this.salesReportData = response.data.data || response.data;
      } catch (error) {
        console.error("Failed to load sales report:", error);
      }
    },

    printReport() {
      const printContents = document.getElementById("printArea").innerHTML;
      const originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
      window.location.reload();
    },

    exportPDF() {
      const doc = new this.$jsPDF();
      doc.text(this.page_title, 14, 20);

      const columns = [
        "SL",
        "Product Name",
        "Unit Price",
        "Quantity",
        "Vat",
        "Discount",
        "Total Amount",
        "Paid",
        "Due",
        "Sale Date",
      ];
      const rows = this.salesReportData.map((item, index) => [
        index + 1,
        item?.product?.name || "",
        item.price,
        item.quantity,
        item.vat,
        item.discount,
        item.total_price,
        item.paid,
        item.due,
        this.$helpers.formatDate(item.created_at),
      ]);

      doc.autoTable({
        head: [columns],
        body: rows,
        startY: 30,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [41, 128, 185] },
      });

      doc.save("sales_report.pdf");
    },

    exportExcel() {
      const worksheetData = this.salesReportData.map((item, index) => ({
        SL: index + 1,
        "Product Name": item?.product?.name || "",
        "Unit Price": item.price,
        Quantity: item.quantity,
        Vat: item.vat,
        Discount: item.discount,
        "Total Amount": item.total_price,
        Paid: item.paid,
        Due: item.due,
        "Sale Date": this.$helpers.formatDate(item.created_at),
      }));

      const worksheet = this.$XLSX.utils.json_to_sheet(worksheetData);
      const workbook = this.$XLSX.utils.book_new();
      this.$XLSX.utils.book_append_sheet(workbook, worksheet, "Sales Report");

      const wbout = this.$XLSX.write(workbook, {
        bookType: "xlsx",
        type: "array",
      });

      this.$saveAs(
        new Blob([wbout], { type: "application/octet-stream" }),
        "sales_report.xlsx"
      );
    },
  },
  created() {
    this.fetchSalesData();
  },
};
</script>

<style scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>
