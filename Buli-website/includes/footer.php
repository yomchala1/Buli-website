    </main><!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="footer-inner">
            <div class="footer-widget footer-about">
                <div class="footer-logo-row">
                    <img src="includes/images.jpg" alt="MGMBPTC Logo" class="footer-logo-img">
                    <span><?php echo __("college_short"); ?></span>
                </div>
                <p><?php echo __("hero_desc"); ?></p>
                <p class="theme-note">&#127775; "Shine Through Education and Technology"</p>
            </div>

            <div class="footer-widget">
                <h4><?php echo __("quick_links"); ?></h4>
                <ul>
                    <li><a href="index.php">&#127968; <?php echo __("nav_home"); ?></a></li>
                    <li><a href="about.php">&#8505; <?php echo __("nav_about"); ?></a></li>
                    <li><a href="academics.php">&#127979; <?php echo __("nav_academics"); ?></a></li>
                    <li><a href="admission.php">&#128196; <?php echo __("nav_admission"); ?></a></li>
                    <li><a href="offices.php">&#127963; Offices</a></li>
                    <li><a href="innovation.php">&#128640; Innovation Hub</a></li>
                </ul>
            </div>

            <div class="footer-widget">
                <h4><?php echo __("resources"); ?></h4>
                <ul>
                    <li><a href="downloads.php">&#128229; <?php echo __("nav_download"); ?></a></li>
                    <li><a href="news.php">&#128240; News &amp; Updates</a></li>
                    <li><a href="staff.php">&#128101; Staff Directory</a></li>
                    <li><a href="library-services.php">&#128218; Library</a></li>
                    <li><a href="student_login.php">&#127891; Student Portal</a></li>
                    <li><a href="contact.php">&#128222; <?php echo __("nav_contact"); ?></a></li>
                </ul>
            </div>

            <div class="footer-widget">
                <h4>&#128205; Contact</h4>
                <div class="footer-contact-items">
                    <p><span class="fc-icon">&#127968;</span> Holeta, Oromia, Ethiopia</p>
                    <p><span class="fc-icon">&#128222;</span> +251 11 123 4567</p>
                    <p><span class="fc-icon">&#128231;</span> info@mgmbptc.edu.et</p>
                    <p><span class="fc-icon">&#128336;</span> Mon–Fri: 8:00 AM–5:00 PM</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> <?php echo __("college_name"); ?>. All Rights Reserved.</p>
            <p class="footer-credits">MGMBPTC ICT Department</p>
        </div>
    </footer>

    <button id="backToTop" title="Back to top" aria-label="Scroll to top">Back to top</button>

    <script src="js/main.js"></script>
    <script>
    /* ---- Sticky shrinking header (always visible) ---- */
    (function(){
        var header = document.querySelector(".main-header");
        var ticker = document.querySelector(".top-ticker-bar");
        window.addEventListener("scroll", function(){
            var y = window.scrollY;
            if(y > 80){
                header.classList.add("header-sticky");
                if(ticker) ticker.style.display = "none";
            } else {
                header.classList.remove("header-sticky");
                if(ticker) ticker.style.display = "";
            }
        },{passive:true});
    })();

    /* ---- Back to top ---- */
    (function(){
        var btn = document.getElementById("backToTop");
        window.addEventListener("scroll",function(){
            btn.classList.toggle("visible", window.scrollY > 400);
        },{passive:true});
        btn.addEventListener("click",function(){
            window.scrollTo({top:0,behavior:"smooth"});
        });
    })();

    /* ---- Scroll-reveal ---- */
    (function(){
        if(!("IntersectionObserver" in window)) return;
        var els = document.querySelectorAll(".card,.dept-card,.pillar-card,.testimonial-card,.partner-item,.stat-item,.data-table tr");
        var io  = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if(e.isIntersecting){
                    e.target.classList.add("sr-visible");
                    io.unobserve(e.target);
                }
            });
        },{threshold:0.10});
        els.forEach(function(el){ el.classList.add("sr-hidden"); io.observe(el); });
    })();
    </script>
</body>
</html>
