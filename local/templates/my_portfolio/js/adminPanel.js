let addPanel = $('.addPanel');
let adminPanel = $('.adminPanel');

addPanel.on('click', function (el) {
    $(adminPanel).toggle('slow', function () {
        if (el.currentTarget.innerHTML == 'Адм.панель ↓') {
            el.currentTarget.innerHTML = 'Адм.панель ↑'
        } else {
            el.currentTarget.innerHTML = 'Адм.панель ↓'
        }
    });
})
