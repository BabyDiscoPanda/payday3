<?php

// Footer for the PayDay 3 News Translator website
// In no circumstances this file can be modified without the consent of the original author.

?>

<footer>
    <p>
        &copy; <?php echo date('Y'); ?> PayDay 3 News Translator. Tous droits réservés.<br>
        Nous vous rappelons que ce site n'est en aucun cas lié à Starbreeze studio.<br>
        <a href="/mentionsLegales.html">Mentions légales</a>
    </p>
</footer>

<script>
    //the footer always on the bottom of the page
    document.addEventListener('DOMContentLoaded', function() {
        const footer = document.querySelector('footer');
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.style.minHeight = `calc(100vh - ${footer.offsetHeight}px)`;
        }
    });

</script>