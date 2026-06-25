// Smooth scroll untuk anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Auto dismiss alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

// Form validation
document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    const checkIn = new Date(this.check_in.value);
    const checkOut = new Date(this.check_out.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if(checkIn < today) {
        e.preventDefault();
        alert('Tanggal check-in tidak boleh kurang dari hari ini!');
        return false;
    }
    
    if(checkOut <= checkIn) {
        e.preventDefault();
        alert('Tanggal check-out harus setelah tanggal check-in!');
        return false;
    }
    
    return true;
});

// Lazy loading images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
});

// Search functionality
function searchProperties() {
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.property-card');
    
    cards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        
        if(title.includes(filter) || description.includes(filter)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}