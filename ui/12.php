<section
    data-bs-version="5.1"
    class="features023 cid-uAktMm3ACt"
    id="metrics-1-uAktMm3ACt">
    <div class="container">
        <div class="row content-row justify-content-center">
            <div
                class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
                <div class="item-wrapper">
                    <div class="title mb-2 mb-md-3">
                        <span class="num mbr-fonts-style display-1">
                            <strong class="counter" data-target="100000">0</strong>+</span>
                    </div>
                    <h4 class="card-title mbr-fonts-style display-5">
                        <strong>Happy Travelers</strong>
                    </h4>
                </div>
            </div>
            <div
                class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
                <div class="item-wrapper">
                    <div class="title mb-2 mb-md-3">
                        <span class="num mbr-fonts-style display-1">
                            <strong >24/7</strong></span>
                    </div>
                    <h4 class="card-title mbr-fonts-style display-5">
                        <strong>Support Available</strong>
                    </h4>
                </div>
            </div>
            <div
                class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
                <div class="item-wrapper">
                    <div class="title mb-2 mb-md-3">
                        <span class="num mbr-fonts-style display-1">
                            <strong class="counter" data-target="99">0</strong>%</span>
                    </div>
                    <h4 class="card-title mbr-fonts-style display-5">
                        <strong>On-Time Departures</strong>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <script>
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        const startCounting = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    counters.forEach(counter => {
                        const updateCount = () => {
                            const target = +counter.getAttribute('data-target');
                            const count = +counter.innerText;
                            const inc = target / speed;

                            if (count < target) {
                                counter.innerText = Math.ceil(count + inc);
                                setTimeout(updateCount, 1);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        updateCount();
                    });
                    observer.unobserve(entry.target);
                }
            });
        };

        const observer = new IntersectionObserver(startCounting, {
            threshold: 0.4
        });

        observer.observe(document.querySelector('.features023'));
    </script>
</section>