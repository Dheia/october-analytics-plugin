



document.addEventListener('DOMContentLoaded', () => {
    let sliders = document.querySelectorAll('[data-synder]');
    if (sliders.length === 0) {
        return;
    }

    [].map.call(sliders, (slider) => {
        let inputs = slider.querySelectorAll('input');
        let min = parseFloat(slider.dataset.sliderMin);
        let max = parseFloat(slider.dataset.sliderMax);

        let handleInput = (input) => {
            if (parseFloat(input.value) <= min && min !== parseFloat(input.min)) {
                input.value = min;
                add = true;
            } else if (parseFloat(input.value) >= max && max !== parseFloat(input.max)) {
                input.value = max;
                add = true;
            } else {
                add = false;
            }

            if (input.type === 'range') {
                slider.classList[add? 'add': 'remove']('range-reached');
            }
            [].map.call(inputs, (el) => el.value = input.value);
        };

        [].map.call(inputs, (input) => {
            input.addEventListener('input', () => { handleInput(input); });
            input.addEventListener('change', () => { handleInput(input); });
        });
    });
});
