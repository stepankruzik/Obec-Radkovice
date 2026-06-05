    <footer class="footer">
        <div class="shell">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="brand footer-brandline">
                        <span class="brand-mark" aria-hidden="true">
                            <img src="img/znak.png" alt="Znak obce Radkovice u Budče" width="32" height="32">
                        </span>
                        <span>Obec Radkovice u Budče</span>
                    </div>
                    <p>Oficiální portál samosprávy. Přehledné informace, dokumenty i kontakty na jednom místě.</p>
                    <div class="socials">
                        <a href="https://www.facebook.com/profile.php?id=61556243986793&locale=cs_CZ" aria-label="Facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M14 8h2V4h-2a4 4 0 0 0-4 4v2H8v4h2v6h4v-6h2.5l.5-4H14V8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/radkoviceubudce/" aria-label="Instagram">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor"/>
                            </svg>
                        </a>
    
                    </div>
                </div>

                <div>
                    <h4>Informace</h4>
                    <div class="footer-links">
                        <a href="#">Povinné informace</a>
                        <a href="#">Ochrana soukromí</a>
                        <a href="#">Prohlášení o přístupnosti</a>
                        <a href="#">Mapa stránek</a>
                    </div>
                </div>

                <div>
                    <h4>Užitečné odkazy</h4>
                    <div class="footer-links">
                        <a href="samosprava.php">Samospráva</a>
                        <a href="uredni-deska.php">Úřední deska</a>
                        <a href="historie-obce.php">Historie obce</a>
                        <a href="fotogalerie.php">Fotogalerie</a>
                        <a href="kontakty.php">Kontakty</a>
                    </div>
                </div>
            </div>

            <div class="copyright">
                <span>&copy; 2026 Obec Radkovice u Budče. Všechna práva vyhrazena.</span>
                <span>Poslední aktualizace: 31. 5. 2026</span>
                <span>Region Vysočina</span>
            </div>
        </div>
    </footer>
    <nav class="mobile-bottom-nav" aria-label="Mobilní navigace">
        <a class="<?php echo $activePage === 'home' ? 'active' : ''; ?>" href="index.php">
            <span aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Aktuality</span>
        </a>
        <a class="<?php echo $activePage === 'uredni-deska' ? 'active' : ''; ?>" href="uredni-deska.php">
            <span aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M8 17 4.5 7.5 8 6l3.5 9.5L8 17Zm4-1.5L8.5 6l3.5-1.5L15.5 14 12 15.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                    <path d="M7 20h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Deska</span>
        </a>
        <a class="<?php echo $activePage === 'fotogalerie' ? 'active' : ''; ?>" href="fotogalerie.php">
            <span aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/>
                    <path d="m8 15 2.5-2.5L13 15l2-2 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="10" r="1.4" fill="currentColor"/>
                </svg>
            </span>
            <span>Galerie</span>
        </a>
        <a class="<?php echo $activePage === 'kontakty' ? 'active' : ''; ?>" href="kontakty.php">
            <span aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M6.8 4h2.6l1.3 4-1.6 1.6a14 14 0 0 0 5 5l1.6-1.6 4 1.3v2.6a1.5 1.5 0 0 1-1.7 1.5C10.7 18 6 13.3 5.3 5.7A1.5 1.5 0 0 1 6.8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </span>
            <span>Kontakt</span>
        </a>
    </nav>
    <script src="assets/js/theme.js" defer></script>
    <script src="assets/js/site.js" defer></script>
</body>
</html>
