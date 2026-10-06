    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const overlay = document.getElementById('overlay');
    const classModal = document.getElementById('class-modal');
    const termBreakModal = document.getElementById('term-break-modal');
    const confirmModal = document.getElementById('confirm-modal');

    // Function to disable background interactions
    function disableBackgroundInteractions() {
        overlay.style.display = 'block';
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto'; // Ensure overlay captures all events
        document.body.style.overflow = 'hidden'; // Prevent scrolling

        // Prevent right-click (context menu) on overlay
        overlay.addEventListener('contextmenu', preventEvent);
        // Prevent left-click on overlay
        overlay.addEventListener('click', preventEvent);
    }

    // Function to enable background interactions
    function enableBackgroundInteractions() {
        overlay.style.display = 'none';
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling

        // Remove event listeners to clean up
        overlay.removeEventListener('contextmenu', preventEvent);
        overlay.removeEventListener('click', preventEvent);
    }

    // Prevent default event behavior
    function preventEvent(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Function to check if any modal is open
    function isAnyModalOpen() {
        return (
            (classModal && classModal.style.display === 'block') ||
            (termBreakModal && termBreakModal.style.display === 'block') ||
            (confirmModal && confirmModal.style.display === 'flex')
        );
    }

    // Observe modal visibility changes
    const observerConfig = { attributes: true, attributeFilter: ['style'] };

    [classModal, termBreakModal, confirmModal].forEach(modal => {
        if (modal) {
            const observer = new MutationObserver(() => {
                if (isAnyModalOpen()) {
                    disableBackgroundInteractions();
                } else {
                    enableBackgroundInteractions();
                }
            });
            observer.observe(modal, observerConfig);
        }
    });

    // Ensure overlay is non-interactive when modals are opened programmatically
    [classModal, termBreakModal, confirmModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('DOMAttrModified', () => {
                if (isAnyModalOpen()) {
                    disableBackgroundInteractions();
                } else {
                    enableBackgroundInteractions();
                }
            });
        }
    });

    // Prevent background clicks when modals are open
    document.addEventListener('click', (e) => {
        if (isAnyModalOpen() && !classModal.contains(e.target) && !termBreakModal.contains(e.target) && !confirmModal.contains(e.target)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // Prevent right-click when modals are open
    document.addEventListener('contextmenu', (e) => {
        if (isAnyModalOpen() && !classModal.contains(e.target) && !termBreakModal.contains(e.target) && !confirmModal.contains(e.target)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
    </script>
