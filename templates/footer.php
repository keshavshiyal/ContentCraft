<footer>
        <p>&copy; <?php echo date("Y"); ?> ContentCraft. All rights reserved.</p>
    </footer>

    <script>
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");
        const dropdowns = document.querySelectorAll(".dropdown");

        // 1. Toggle Mobile Menu
        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        // 2. Handle Dropdown Clicks on Mobile
        dropdowns.forEach(dropdown => {
            const toggleBtn = dropdown.querySelector(".dropdown-toggle");
            
            if(toggleBtn){
                toggleBtn.addEventListener("click", (e) => {
                    if (window.innerWidth <= 768) {
                        e.preventDefault(); 
                        dropdown.classList.toggle("active");
                    }
                });
            }
        });

        // 3. Close menu when clicking standard links
        document.querySelectorAll(".nav-link").forEach(link => {
            if (!link.classList.contains("dropdown-toggle")) {
                link.addEventListener("click", () => {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                });
            }
        });

        // 4. Reset on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                navMenu.classList.remove("active");
                hamburger.classList.remove("active");
                dropdowns.forEach(d => d.classList.remove("active"));
            }
        });
        
        function focusMenu() {
            if (window.innerWidth > 768) {
                const dropdown = document.getElementById('generateDropdown');
                // Highlight effect logic could go here
                alert("Hover over 'Generate' in the menu to begin!");
            } else {
                hamburger.click();
            }
        }
    </script>

</body>
</html>