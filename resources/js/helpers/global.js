import moment from 'moment';
export function emptyImage(url) {
    return url ?? '';
}
export function formatDate(dateStr) {
    if (!dateStr) return '';
    return moment(dateStr).format('D MMMM YYYY');
}