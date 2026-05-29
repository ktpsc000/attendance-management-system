document.addEventListener('DOMContentLoaded', function() {

    function updateClock() {
        const now = new Date();

        const week = ['日', '月', '火', '水', '木', '金', '土'];

        document.getElementById('current-date').textContent =
            `${now.getFullYear()}年${now.getMonth() + 1}月${now.getDate()}日(${week[now.getDay()]})`;

        document.getElementById('current-time').textContent =
            `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    }

    updateClock();

    setInterval(updateClock, 1000);
});