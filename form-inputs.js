

window.addEventListener('load', () => {
    document.body.addEventListener('click', event => {
        const e = event.target;
        if (!e.matches('.input-number *')) {
            return true;
        }
        const input = e.closest('.input-number').querySelector('input');
        if (e.matches('.input-number > a')) {
            if (e.matches('.minus')) {
                input.value = Number(input.value) - 1;
            }
            if (e.matches('.plus')) {
                input.value = Number(input.value) + 1;
            }
            input.focus();
            // input.select();
        }
    });
});