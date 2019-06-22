export const debounce = (func, wait, immediate = false) => {
    let timeout;
    return (e) => {
        const later = () => {
            timeout = null;
            if (!immediate) {
                func.apply(this, [e, ...arguments]);
            }
        };

        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);

        if (callNow) {
            func.apply(this, [e, ...arguments]);
        }
    };
}