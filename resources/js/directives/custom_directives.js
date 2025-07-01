const xNumbersOnly = {
    mounted(el) {
        el.addEventListener("input", (e) => {
            let value = e.target.value;
            const numericValue = value.replace(/[^0-9.]/g, "");
            const cleanedValue = numericValue.replace(/^([^\.]*\.)|\./g, "$1");
            if (value !== cleanedValue) {
                e.target.value = cleanedValue;
                e.target.dispatchEvent(new Event("input"));
            }
        });
    },
};

export default {
    xNumbersOnly,
};
