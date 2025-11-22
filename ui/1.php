<section
        data-bs-version="5.1"
        class="menu menu2 cid-uAktMlZck6"
        once="menu"
        id="menu-5-uAktMlZck6">
        <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <span class="navbar-logo">
                        <a href="#home">
                            <img
                                src="assets/img/logo.jpg"
                                alt="Busify Logo"
                                style="height: 4rem; width: 8vw;" />
                        </a>
                    </span>  
                </div>
                
                <button
                    class="navbar-toggler"
                    type="button"
                    data-toggle="collapse" 
                    data-bs-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav nav-dropdown nav-right" data-app-modern-menu="true">
                        <li class="nav-item">
                            <a class="nav-link link text-black display-4" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link link text-black display-4"
                                href="#reserve">Reserve Your Seat</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link link text-black display-4"
                                href="#pricing">Ticket Plans</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link link text-black display-4"
                                href="#contact">Contact</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle text-black display-4"
                                href="#"
                                id="loginDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Staff Login
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="loginDropdown">
                                <li><a class="dropdown-item" href="employee/emp-login.php">Employee Login</a></li>
                                <li><a class="dropdown-item" href="admin/emp-login.php">Admin Login</a></li>
                            </ul>
                        </li>
                    </ul>

                    <div class="navbar-buttons mbr-section-btn">
                        <a class="btn btn-primary display-4" href="pass-login.php">Passenger Login</a>
                    </div>
                </div>
            </div>
        </nav>
        <script>
            // Function to update active menu item based on scroll position
            function updateActiveMenu() {
                const sections = document.querySelectorAll('section[id]');
                const navLinks = document.querySelectorAll('.nav-link');
                
                let currentSection = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (window.scrollY >= (sectionTop - sectionHeight/3)) {
                        currentSection = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${currentSection}`) {
                        link.classList.add('active');
                    }
                });
            }

            // Add scroll event listener
            window.addEventListener('scroll', updateActiveMenu);
            
            // Update active menu on page load
            document.addEventListener('DOMContentLoaded', updateActiveMenu);

            // Update active menu when clicking on nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
        </script>
        <style>
            .nav-link.active {
                color: #6592e6 !important;
                font-weight: bold;
            }
        </style>
    </section>