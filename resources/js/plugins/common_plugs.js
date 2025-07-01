import Swal from 'sweetalert2';
import moment from 'moment';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import axios from "axios";


export default {
    install(app) {
        app.config.globalProperties.$Swal = Swal;
        app.config.globalProperties.$moment = moment;
        app.config.globalProperties.$jsPDF = jsPDF;
        app.config.globalProperties.$XLSX = XLSX;
        app.config.globalProperties.$saveAs = saveAs;
        app.config.globalProperties.$axios = axios;
    },
};