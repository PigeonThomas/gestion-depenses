document.addEventListener('DOMContentLoaded', () => {
    const colorInput = document.querySelector('[data-categorie-color-input="true"]');
    const colorPreview = document.querySelector('[data-categorie-color-preview="true"]');
    const colorValue = document.querySelector('[data-categorie-color-value="true"]');
    if (colorInput && colorPreview && colorValue) {
        const defaultColor = colorPreview.dataset.defaultColor || '';

        const updateColorPreview = () => {
            const color = colorInput.value || defaultColor;

            colorPreview.style.backgroundColor = color;
            colorValue.textContent = color;
        };

        colorInput.addEventListener('input', updateColorPreview);
        colorInput.addEventListener('change', updateColorPreview);

        updateColorPreview();
    }
});