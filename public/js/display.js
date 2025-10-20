// JAM & TANGGAL
document.addEventListener('DOMContentLoaded', () => {
    const clock = document.getElementById('clock');
    const day = document.getElementById('day');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

    function updateClock() {
        const now = new Date();
        clock.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        day.textContent = now.toLocaleDateString('id-ID', options);
    }

    updateClock();
    setInterval(updateClock, 1000);
});

// AutoScroll

function autoScrollMissedCards() {
    const container = document.querySelector('.missed-cards-container');
    const cards = document.querySelector('.missed-cards');
    if (!container || !cards) return;

    const cardCount = cards.children.length;
    const visibleCount = 3;
    if (cardCount <= visibleCount) return; // ga perlu scroll

    let position = 0;
    let direction = 1; // 1 = scroll kanan, -1 = scroll kiri

    setInterval(() => {
        const maxScroll = cards.scrollWidth - container.clientWidth;
        position += direction * 1; // kecepatan scroll pixel per interval

        if (position >= maxScroll) direction = -1; // balik kiri
        if (position <= 0) direction = 1; // balik kanan

        cards.style.transform = `translateX(-${position}px)`;
    }, 20);
}

document.addEventListener('DOMContentLoaded', autoScrollMissedCards);
