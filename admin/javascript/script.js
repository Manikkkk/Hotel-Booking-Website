// Admin panel navigation
document.addEventListener('DOMContentLoaded', function() {
    const btns = document.querySelectorAll('.pagebtn');
    const frames = document.querySelectorAll('.content-frame');

    // Function to activate the selected frame
    const frameActive = function(manual) {
        // Remove active class from all buttons and frames
        btns.forEach((btn) => {
            btn.classList.remove('active');
        });
        frames.forEach((slide) => {
            slide.classList.remove('active');
        });

        // Add active class to selected button and frame
        btns[manual].classList.add('active');
        frames[manual].classList.add('active');
    };

    // Add click event to each button
    btns.forEach((btn, i) => {
        btn.addEventListener('click', () => {
            frameActive(i);
        });
    });

    // Resize iframe content when window resizes
    window.addEventListener('resize', function() {
        adjustFrameHeight();
    });

    // Adjust iframe height on load
    adjustFrameHeight();

    // Sidebar toggle for responsive design
    const sidebarToggle = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');
    
    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
    }
});

// Function to adjust iframe height
function adjustFrameHeight() {
    const frames = document.querySelectorAll('.content-frame');
    const topNavbar = document.querySelector('.top-navbar');
    const windowHeight = window.innerHeight;
    
    if (topNavbar) {
        const navbarHeight = topNavbar.offsetHeight;
        frames.forEach(frame => {
            frame.style.height = (windowHeight - navbarHeight) + 'px';
        });
    }
}
