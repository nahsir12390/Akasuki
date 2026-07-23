<script>
document.addEventListener('DOMContentLoaded', function() {
    const scroller = document.getElementById('tutorialScroller');
    const leftBtn = document.querySelector('.left-scroll');
    const rightBtn = document.querySelector('.right-scroll');

    checkScrollPosition(); // Initial check

    scroller.addEventListener('scroll', checkScrollPosition);

    leftBtn.addEventListener('click', () => {
        scroller.scrollBy({ left: -200, behavior: 'smooth' });
    });

    rightBtn.addEventListener('click', () => {
        scroller.scrollBy({ left: 200, behavior: 'smooth' });
    });

    function checkScrollPosition() {
        leftBtn.classList.toggle('hidden', scroller.scrollLeft <= 0);
        rightBtn.classList.toggle('hidden', scroller.scrollLeft >= scroller.scrollWidth - scroller.clientWidth - 1);
    }

    window.addEventListener('resize', checkScrollPosition);
});
</script>
