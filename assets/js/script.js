const revealElements = document.querySelectorAll(
    ".reveal-left, .reveal-right"
);

const revealObserver = new IntersectionObserver(
    (entries) => {

        entries.forEach((entry) => {

            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }

        });

    },
    {
        threshold: 0.2
    }
);

revealElements.forEach((element) => {
    revealObserver.observe(element);
});