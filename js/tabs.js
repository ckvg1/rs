document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // dezaktwyuje wszystkie zakldadki i usuwa klase
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // aktywuje zakladke kliknieta i dodaje klase active
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            const iframe = button.getAttribute('data-src');
            document.getElementById(tabId).innerHTML = `<iframe src="${iframe}" frameborder="0" width="100%" height="100%"></iframe>`;
            document.getElementById(tabId).classList.add('active');
        });
    });
});