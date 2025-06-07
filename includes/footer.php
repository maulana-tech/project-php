<?php if (is_logged_in()): ?>
            </main>
        </div>
    </div>
<?php else: ?>
        </div>
    </div>
<?php endif; ?>

<!-- JavaScript for mobile menu (for logged in users) -->
<?php if (is_logged_in()): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        if (closeMobileMenuButton && mobileMenu) {
            closeMobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
        
        if (mobileMenuOverlay && mobileMenu) {
            mobileMenuOverlay.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
    });
</script>
<?php endif; ?>
</body>
</html>