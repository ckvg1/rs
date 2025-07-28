document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // dezaktwyuje wszystkie zakldadki i usuwa klase active
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // aktywuje zakladke kliknieta i dodaje klase active
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
function hamburger(x) {
    x.classList.toggle("change");
    tabsList = document.querySelector('.tabs-list')
    if(tabsList.style.right == "0px"){
        tabsList.style.right = "-250px"
    }
    else{
        tabsList.style.right = "0px"
    }
}